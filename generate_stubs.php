<?php
require 'vendor/autoload.php';

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpParser\PhpVersion;
use PhpParser\Modifiers;

class StubGenerator extends NodeVisitorAbstract {
    private $prettyPrinter;
    private $stubs = [];
    private $currentClass = null;
    private $currentNamespace = '';
    private $processedClasses = [];
    private $classMethods = [];
    private $classProperties = [];
    private $classConstants = [];
    private $isNamespaceWrapped = false;

    public function __construct() {
        $this->prettyPrinter = new PrettyPrinter\Standard;
    }

    public function enterNode(Node $node) {
        // Check if the node is a namespace declaration
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->isNamespaceWrapped = !empty($node->stmts);
            $nodeWithoutStmts = clone $node;
            $nodeWithoutStmts->stmts = [];
            $this->currentNamespace = str_replace(";\n", "", $this->prettyPrinter->prettyPrint([$nodeWithoutStmts]));
        }

        // Check if the node is a class, interface, trait, or enum declaration
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Enum_) {
            // Ensure namespacedName is set
            if (isset($node->namespacedName)) {
                $className = $node->namespacedName->toString();

                // If the class has already been processed, skip it
                if (isset($this->processedClasses[$className])) {
                    $this->currentClass = null; // Avoid adding methods/properties/constants to this class again
                    return;
                }

                // Store only the class signature without its methods, properties, or constants
                $nodeWithoutStmts = clone $node;
                $nodeWithoutStmts->stmts = []; // Clear all methods, properties, and constants
                $this->currentClass = $className;

                $prettyPrintClass = $this->prettyPrinter->prettyPrint([$nodeWithoutStmts]);
                $prettyPrintClass = str_replace("\n", "\n\t", $prettyPrintClass);
                if ($this->isNamespaceWrapped) {
                    $this->processedClasses[$className] = $this->currentNamespace . " {\n\t" . $prettyPrintClass . "\n}\n";
                } else {
                    $this->processedClasses[$className] = "namespace {\n\t" . $prettyPrintClass . "\n}\n";
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
            if ($methodModifiers & Modifiers::PUBLIC || $methodModifiers & Modifiers::PROTECTED) {
                // Clear the body of the method
                $node->stmts = [];

                // Store the method within the class
                if ($this->currentClass !== null) {
                    $indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $prettyPrintClassMethod = str_replace("\n}", "}", $this->prettyPrinter->prettyPrint([$node]));
                    $prettyPrintClassMethod = $indentation . str_replace("\n{", " {", $prettyPrintClassMethod);
                    $prettyPrintClassMethod = str_replace("\n", "\n{$indentation}", $prettyPrintClassMethod);
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
            if ($propertyModifiers & Modifiers::PUBLIC || $propertyModifiers & Modifiers::PROTECTED) {
                if ($this->currentClass !== null) {
                    $indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $prettyPrintProperty = $indentation . $this->prettyPrinter->prettyPrint([$node]);
                    $prettyPrintProperty = str_replace("\n", "\n{$indentation}", $prettyPrintProperty);
                    $this->classProperties[$this->currentClass][] = $prettyPrintProperty;
                }
            }
        }

        // Check if the node is a class constant
        if ($node instanceof Node\Stmt\ClassConst) {
            $constModifiers = $node->flags;
            // Include only public and protected constants
            if ($constModifiers & Modifiers::PUBLIC || $constModifiers & Modifiers::PROTECTED) {
                if ($this->currentClass !== null) {
                    $indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                    $prettyPrintConst = $indentation . $this->prettyPrinter->prettyPrint([$node]);
                    $prettyPrintConst = str_replace("\n", "\n{$indentation}", $prettyPrintConst);
                    $this->classConstants[$this->currentClass][] = $prettyPrintConst;
                }
            }
        }

        // Check if the node is a global constant defined using 'define'
        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name && $node->name->toString() === 'define') {
            if (isset($node->args[0]->value) && $node->args[0]->value instanceof Node\Scalar\String_) {
                $constantName = $node->args[0]->value->value;
                $constantValue = isset($node->args[1]->value) ? $this->prettyPrinter->prettyPrintExpr($node->args[1]->value) : 'null';
                $this->stubs[] = "define('$constantName', $constantValue);";
            }
        }
    }

    public function leaveNode(Node $node) {
        // When leaving a class, interface, trait, or enum node, finalize the class stub
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Trait_ || $node instanceof Node\Stmt\Enum_) {
            if ($this->currentClass !== null) {
                $indentation = $this->isNamespaceWrapped ? "\t\t" : "\t";
                // Combine the class signature with its methods, properties, and constants
                $classProperties = implode("\n", $this->classProperties[$this->currentClass]);
                $classConstants = implode("\n", $this->classConstants[$this->currentClass]);
                $classMethods = implode("\n", $this->classMethods[$this->currentClass]);

                $classBody = trim("{$classConstants}\n{$classProperties}\n{$classMethods}");

                // Use preg_replace to insert properties, constants, and methods inside the class body
                $pattern = "/(.*{\n)(.*})/s";
                $classStub = preg_replace($pattern, "\${1}{$indentation}{$classBody}\n\${2}", $this->processedClasses[$this->currentClass]);

                // Save the class stub
                $this->stubs[] = $classStub;

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

    public function generateStubs($directory) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $parser = (new ParserFactory)->createForVersion(PhpVersion::fromString('7.3')); // Specify the PHP version
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

    public function saveStubs($outputDir) {
        file_put_contents($outputDir . '/mediawiki-stubs.php', "<?php\n\n" . implode("\n", $this->stubs) . "\n");
    }
}

$generator = new StubGenerator();
$generator->generateStubs('../mediawiki-core');
$generator->saveStubs('./stubs');
