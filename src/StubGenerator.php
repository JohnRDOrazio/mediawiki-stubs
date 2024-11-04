<?php

namespace Johnrdorazio\MediaWikiStubs;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpParser\PhpVersion;
use PhpParser\Modifiers;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;

class StubGenerator extends NodeVisitorAbstract
{
    private const EXCLUDE_DIRS = [
        'cache',
        'docs',
        'extensions',
        '.git',
        'images',
        'languages',
        'mw-config',
        '.phan',
        'resources',
        'skins',
        'tests'
    ];
    private PrettyPrinter\Standard $prettyPrinter;
    private bool $isNamespaceWrapped   = false;
    private ?string $currentClass      = null;
    private string $currentNamespace   = '';
    private array $processedClasses    = [];
    private array $processedNamespaces = ['namespace' => []];
    private array $classMethods        = [];
    private array $classProperties     = [];
    private array $classConstants      = [];
    private array $globalConstants     = [];
    private array $stubs               = [];
    private array $rawMethods          = [];
    private array $rawClasses          = [];
    private array $prettyClasses       = [];
    private array $rawNamespaces       = [];
    private array $prettyNamespaces    = [];
    private array $rawConstants        = [];
    private array $rawProperties       = [];

    public function __construct()
    {
        $this->prettyPrinter = new PrettyPrinter\Standard();
    }

    public function enterNode(Node $node)
    {
        // Check if the node is a namespace declaration
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->isNamespaceWrapped = !empty($node->stmts);
            $nodeWithoutStmts = clone $node;
            $nodeWithoutStmts->stmts = [];
            $this->rawNamespaces[] = $nodeWithoutStmts;
            $pattern = '/^(?:.|\n)*(namespace .*?);\n$/';
            $this->currentNamespace = preg_replace($pattern, '$1', $this->prettyPrinter->prettyPrint([$nodeWithoutStmts]));
            if (false === array_key_exists($this->currentNamespace, $this->processedNamespaces)) {
                $this->prettyNamespaces[] = [
                    'prettyPrint' => $this->prettyPrinter->prettyPrint([$nodeWithoutStmts]),
                    'clean' => $this->currentNamespace
                ];
                $this->processedNamespaces[$this->currentNamespace] = [];
            }
        }

        // Check if the node is a class, interface, trait, or enum declaration
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Enum_) {
            // Ensure namespacedName is set
            if (isset($node->namespacedName)) {
                $className = $node->namespacedName->toString();
                // If the class has already been processed, skip it
                if (in_array($className, $this->processedClasses)) {
                    $this->currentClass = null; // Avoid adding methods/properties/constants to this class again
                    return;
                }

                // Store only the class signature without its methods, properties, or constants
                $nodeWithoutStmts = clone $node;
                $nodeWithoutStmts->stmts = []; // Clear all methods, properties, and constants
                $this->rawClasses[$className] = [
                    'raw' => $nodeWithoutStmts,
                    'namespace' => $this->currentNamespace
                ];
                $this->currentClass = $className;

                $prettyPrintClass = $this->prettyPrinter->prettyPrint([$nodeWithoutStmts]);
                $prettyPrintClass = str_replace("\n", "\n\t", $prettyPrintClass);

                $this->processedClasses[] = $className;
                if ($this->isNamespaceWrapped) {
                    //$this->processedClasses[$className] = $this->currentNamespace . " {\n\t" . $prettyPrintClass . "\n}\n";
                    $this->processedNamespaces[$this->currentNamespace][$className] = "\t" . $prettyPrintClass;
                } else {
                    $this->processedNamespaces['namespace'][$className] = "\t" . $prettyPrintClass;
                    //$this->processedClasses[$className] = "namespace {\n\t" . $prettyPrintClass . "\n}\n";
                }

                $this->classMethods[$className] = [];
                $this->classProperties[$className] = [];
                $this->classConstants[$className] = [];
            }
        }

        // Check if the node is a class method
        if ($node instanceof Node\Stmt\ClassMethod) {
            $methodModifiers = $node->flags;
            // Include only public and protected methods, and exclude private methods
            if ($methodModifiers & (Modifiers::PUBLIC | MODIFIERS::PROTECTED)) {
                // Clear the body of the method
                $node->stmts = [];

                // Store the method within the class
                if ($this->currentClass !== null) {
                    $this->rawMethods[] = $node;
                    //$indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $indentation = "\t\t";
                    $prettyPrintClassMethod = str_replace("\n}", "}", $this->prettyPrinter->prettyPrint([$node]));
                    $prettyPrintClassMethod = $indentation . str_replace("\n{", " {", $prettyPrintClassMethod);
                    $prettyPrintClassMethod = str_replace("\n", "\n{$indentation}", $prettyPrintClassMethod);
                    // Escape any character combinations in the Doc Block that could be confused as a capture group replacement
                    $prettyPrintClassMethod = preg_replace('/(\$[1-9])/', '\\\$1', $prettyPrintClassMethod);
                    $this->classMethods[$this->currentClass][] = $prettyPrintClassMethod;
                }
            }
        }

        // Check if the node is a standalone function declaration
        if ($node instanceof Node\Stmt\Function_) {
            // Clear the body of the function
            $node->stmts = [];

            // Store the function directly
            $prettyPrintFunction = str_replace("\n}", "}", $this->prettyPrinter->prettyPrint([$node]));
            $prettyPrintFunction = str_replace("\n{", " {", $prettyPrintFunction);
            $this->stubs[] = $prettyPrintFunction;
        }

        // Check if the node is a class property
        if ($node instanceof Node\Stmt\Property) {
            $propertyModifiers = $node->flags;
            // Include only public and protected properties
            if ($propertyModifiers & (Modifiers::PUBLIC | MODIFIERS::PROTECTED)) {
                if ($this->currentClass !== null) {
                    $this->rawProperties[] = $node;
                    //$indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $indentation = "\t\t";
                    $prettyPrintProperty = $indentation . $this->prettyPrinter->prettyPrint([$node]);
                    $prettyPrintProperty = str_replace("\n", "\n{$indentation}", $prettyPrintProperty);
                    // Escape any character combinations in the Doc Block that could be confused as a capture group replacement
                    $prettyPrintProperty = preg_replace('/(\$[1-9])/', '\\\$1', $prettyPrintProperty);
                    $this->classProperties[$this->currentClass][] = $prettyPrintProperty;
                }
            }
        }

        // Check if the node is a class constant
        if ($node instanceof Node\Stmt\ClassConst) {
            $constModifiers = $node->flags;
            // Include only public and protected constants
            if ($constModifiers & (Modifiers::PUBLIC | MODIFIERS::PROTECTED)) {
                if ($this->currentClass !== null) {
                    $this->rawConstants[] = $node;
                    //$indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $indentation = "\t\t";
                    $prettyPrintConst = $indentation . $this->prettyPrinter->prettyPrint([$node]);
                    $prettyPrintConst = str_replace("\n", "\n{$indentation}", $prettyPrintConst);
                    // Escape any character combinations in the Doc Block that could be confused as a capture group replacement
                    $prettyPrintConst = preg_replace('/(\$[1-9])/', '\\\$1', $prettyPrintConst);
                    $prettyPrintConst = preg_replace('/(\\\1)/', '\\\$1', $prettyPrintConst);
                    $this->classConstants[$this->currentClass][] = $prettyPrintConst;
                }
            }
        }

        // Check if the node is a global constant defined using 'define'
        if (
            $node instanceof Node\Expr\FuncCall
            && $node->name instanceof Node\Name
            && $node->name->toString() === 'define'
        ) {
            if (isset($node->args[0]->value) && $node->args[0]->value instanceof Node\Scalar\String_) {
                $constantName = $node->args[0]->value->value;
                if (false === in_array($constantName, $this->globalConstants)) {
                    $constantValue = isset($node->args[1]->value) ? $this->prettyPrinter->prettyPrintExpr($node->args[1]->value) : 'null';
                    $this->stubs[] = "define('$constantName', $constantValue);";
                    $this->globalConstants[] = $constantName;
                }
            }
        }
    }

    public function leaveNode(Node $node)
    {
        // When leaving a class, interface, trait, or enum node, finalize the class stub
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Enum_) {
            if ($this->currentClass !== null) {
                //$indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                $indentation = "\t\t";
                // Combine the class signature with its methods, properties, and constants
                $classConstants = implode("\n", $this->classConstants[$this->currentClass]);
                $classProperties = implode("\n", $this->classProperties[$this->currentClass]);
                $classMethods = implode("\n", $this->classMethods[$this->currentClass]);

                $classBody = trim("{$classConstants}\n{$classProperties}\n{$classMethods}");
                $namespace = $this->currentNamespace && !empty($this->currentNamespace) ? $this->currentNamespace : 'namespace';
                // self::debugWrite("*******************************************************\n");
                // self::debugWrite("-----------------nameSpace {$namespace}----------------\n");
                // self::debugWrite($this->processedNamespaces[$namespace][$this->currentClass]);
                // self::debugWrite("\n\n-----------------classBody {$this->currentClass}--------------------\n");
                // self::debugWrite($classBody . "\n\n");

                if (!empty($classBody)) {
                    // Use preg_replace to insert properties, constants, and methods inside the class body
                    $pattern = '/^((?:.|\n|\t)*\n\t{)(\n\t})$/';
                    $currentClassValue = $this->processedNamespaces[$namespace][$this->currentClass];
                    $this->processedNamespaces[$namespace][$this->currentClass] = preg_replace($pattern, '$1' . "\n{$indentation}{$classBody}" . '$2', $currentClassValue);
                    // self::debugWrite("-----------------nameSpace {$namespace} WITH classBody-----\n");
                    // self::debugWrite($this->processedNamespaces[$namespace][$this->currentClass] . "\n");
                }
                // self::debugWrite("*******************************************************\n\n\n");

                // Reset the current class, its methods, properties, and constants
                $this->currentClass = null;
            }
        }

        // When leaving a namespace node, clear the current namespace
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->currentNamespace = '';
            $this->isNamespaceWrapped = false;
        }
    }

    public function generateStubs($directory)
    {
        $dirIterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        $filterIterator = new RecursiveCallbackFilterIterator($dirIterator, function ($current) {
            // Exclude unwanted directories
            if ($current->isDir() && in_array($current->getFilename(), self::EXCLUDE_DIRS)) {
                return false;
            }
            return true;
        });
        $files = new RecursiveIteratorIterator($filterIterator);
        $parser = (new ParserFactory())->createForVersion(PhpVersion::fromString('7.4')); // Specify the PHP version
        $traverser = new NodeTraverser();

        // Add the NameResolver visitor to populate fully-qualified names
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($this);

        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $code = file_get_contents($file->getRealPath());

                try {
                    $stmts = $parser->parse($code);
                    $traverser->traverse($stmts);
                } catch (Error $e) {
                    // Handle parse errors (log them, skip file, etc.)
                    echo "Parse error in file {$file->getRealPath()}: {$e->getMessage()}\n";
                }
            }
        }
    }

    public function saveStubs($outputDir)
    {
        $finalStr = "";
        foreach ($this->processedNamespaces as $namespace => $classes) {
            $finalStr .= "{$namespace} {\n" . implode("\n", $classes) . "\n}\n";
        }
        if (!is_dir($outputDir)) {
            mkdir($outputDir);
        }
        file_put_contents($outputDir . '/mediawiki-globals.php', "<?php\n\n" . implode("\n", $this->stubs));
        file_put_contents($outputDir . '/mediawiki-stubs.php', "<?php\n\n" . $finalStr);
        // file_put_contents($outputDir . '/mediawiki-methods-pretty.json', json_encode($this->classMethods, JSON_PRETTY_PRINT));
        // file_put_contents($outputDir . '/mediawiki-classes-raw.json', json_encode($this->rawClasses, JSON_PRETTY_PRINT));
        // file_put_contents($outputDir . '/mediawiki-namespaces-raw.json', json_encode($this->rawNamespaces, JSON_PRETTY_PRINT));
        // file_put_contents($outputDir . '/mediawiki-namespaces-pretty.json', json_encode($this->prettyNamespaces, JSON_PRETTY_PRINT));
        // file_put_contents($outputDir . '/mediawiki-class-constants-raw.json', json_encode($this->rawConstants, JSON_PRETTY_PRINT));
        // file_put_contents($outputDir . '/mediawiki-class-constants-pretty.json', json_encode($this->classConstants, JSON_PRETTY_PRINT));
        file_put_contents($outputDir . '/mediawiki-class-properties-raw.json', json_encode($this->rawProperties, JSON_PRETTY_PRINT));
        file_put_contents($outputDir . '/mediawiki-class-properties-pretty.json', json_encode($this->classProperties, JSON_PRETTY_PRINT));
    }

    private static function debugWrite(string $string)
    {
        $file = __DIR__ . '/debug.log';
        if (!file_exists($file)) {
            touch($file);
        }
        file_put_contents($file, $string, FILE_APPEND);
    }
}
