<?php

define('MW_REST_API', true);
define('MW_ENTRY_POINT', 'rest');
define('MW_NO_SESSION', 1);
define('MW_API', true);
define('MW_NO_EXTENSION_MESSAGES', 1);
define('MW_USE_CONFIG_SCHEMA_CLASS', 1);
define('MEDIAWIKI', true);
// Define a function, since we can't put a closure or object
// reference into MW_FINAL_SETUP_CALLBACK.
function wfMaintenanceSetup(\MediaWiki\Settings\SettingsBuilder $settingsBuilder) {}
define('MW_FINAL_SETUP_CALLBACK', 'wfMaintenanceSetup');
define('RUN_MAINTENANCE_IF_MAIN', __DIR__ . '/doMaintenance.php');
define('DO_MAINTENANCE', RUN_MAINTENANCE_IF_MAIN);
define('MW_CONFIG_CALLBACK', [Installer::class, 'overrideConfig']);
define('MEDIAWIKI_INSTALL', true);
// Define a function, since we can't put a closure or object
// reference into MW_FINAL_SETUP_CALLBACK.
function wfMaintenanceRunSetup(\MediaWiki\Settings\SettingsBuilder $settingsBuilder) {}
define('MW_NO_OUTPUT_COMPRESSION', 1);
define('MW_SERVICE_BOOTSTRAP_COMPLETE', 1);
define('DBO_DEBUG', IDatabase::DBO_DEBUG);
define('DBO_NOBUFFER', IDatabase::DBO_NOBUFFER);
define('DBO_IGNORE', IDatabase::DBO_IGNORE);
define('DBO_TRX', IDatabase::DBO_TRX);
define('DBO_DEFAULT', IDatabase::DBO_DEFAULT);
define('DBO_PERSISTENT', IDatabase::DBO_PERSISTENT);
define('DBO_SYSDBA', IDatabase::DBO_SYSDBA);
define('DBO_DDLMODE', IDatabase::DBO_DDLMODE);
define('DBO_SSL', IDatabase::DBO_SSL);
define('DBO_COMPRESS', IDatabase::DBO_COMPRESS);
define('DB_REPLICA', ILoadBalancer::DB_REPLICA);
define('DB_PRIMARY', ILoadBalancer::DB_PRIMARY);
define('MEDIATYPE_UNKNOWN', 'UNKNOWN');
define('MEDIATYPE_BITMAP', 'BITMAP');
define('MEDIATYPE_DRAWING', 'DRAWING');
define('MEDIATYPE_AUDIO', 'AUDIO');
define('MEDIATYPE_VIDEO', 'VIDEO');
define('MEDIATYPE_MULTIMEDIA', 'MULTIMEDIA');
define('MEDIATYPE_OFFICE', 'OFFICE');
define('MEDIATYPE_TEXT', 'TEXT');
define('MEDIATYPE_EXECUTABLE', 'EXECUTABLE');
define('MEDIATYPE_ARCHIVE', 'ARCHIVE');
define('MEDIATYPE_3D', '3D');
/**
 * Load an extension
 *
 * This queues an extension to be loaded through
 * the ExtensionRegistry system.
 *
 * @param string $ext Name of the extension to load
 * @param string|null $path Absolute path of where to find the extension.json file
 * @since 1.25
 */
function wfLoadExtension($ext, $path = null) {}
/**
 * Load multiple extensions at once
 *
 * Same as wfLoadExtension, but more efficient if you
 * are loading multiple extensions.
 *
 * If you want to specify custom paths, you should interact with
 * ExtensionRegistry directly.
 *
 * @see wfLoadExtension
 * @param string[] $exts Array of extension names to load
 * @since 1.25
 */
function wfLoadExtensions(array $exts) {}
/**
 * Load a skin
 *
 * @see wfLoadExtension
 * @param string $skin Name of the extension to load
 * @param string|null $path Absolute path of where to find the skin.json file
 * @since 1.25
 */
function wfLoadSkin($skin, $path = null) {}
/**
 * Load multiple skins at once
 *
 * @see wfLoadExtensions
 * @param string[] $skins Array of extension names to load
 * @since 1.25
 */
function wfLoadSkins(array $skins) {}
/**
 * Like array_diff( $arr1, $arr2 ) except that it works with two-dimensional arrays.
 * @param string[]|array[] $arr1
 * @param string[]|array[] $arr2
 * @return array
 */
function wfArrayDiff2($arr1, $arr2) {}
/**
 * Merge arrays in the style of PermissionManager::getPermissionErrors, with duplicate removal
 * e.g.
 *     wfMergeErrorArrays(
 *       [ [ 'x' ] ],
 *       [ [ 'x', '2' ] ],
 *       [ [ 'x' ] ],
 *       [ [ 'y' ] ]
 *     );
 * returns:
 *     [
 *       [ 'x', '2' ],
 *       [ 'x' ],
 *       [ 'y' ]
 *     ]
 *
 * @deprecated since 1.43 Use StatusValue::merge() instead
 * @param array[] ...$args
 * @return array
 */
function wfMergeErrorArrays(...$args) {}
/**
 * Insert an array into another array after the specified key. If the key is
 * not present in the input array, it is returned without modification.
 *
 * @param array $array
 * @param array $insert The array to insert.
 * @param mixed $after The key to insert after.
 * @return array
 */
function wfArrayInsertAfter(array $array, array $insert, $after) {}
/**
 * Recursively converts the parameter (an object) to an array with the same data
 *
 * @phpcs:ignore MediaWiki.Commenting.FunctionComment.ObjectTypeHintParam
 * @param object|array $objOrArray
 * @param bool $recursive
 * @return array
 */
function wfObjectToArray($objOrArray, $recursive = true) {}
/**
 * Get a random decimal value in the domain of [0, 1), in a way
 * not likely to give duplicate values for any realistic
 * number of articles.
 *
 * @note This is designed for use in relation to Special:RandomPage
 *       and the page_random database field.
 *
 * @return string
 */
function wfRandom() {}
/**
 * Get a random string containing a number of pseudo-random hex characters.
 *
 * @note This is not secure, if you are trying to generate some sort
 *       of token please use MWCryptRand instead.
 *
 * @param int $length The length of the string to generate
 * @return string
 * @since 1.20
 */
function wfRandomString($length = 32) {}
/**
 * We want some things to be included as literal characters in our title URLs
 * for prettiness, which urlencode encodes by default.  According to RFC 1738,
 * all of the following should be safe:
 *
 * ;:@&=$-_.+!*'(),
 *
 * RFC 1738 says ~ is unsafe, however RFC 3986 considers it an unreserved
 * character which should not be encoded. More importantly, google chrome
 * always converts %7E back to ~, and converting it in this function can
 * cause a redirect loop (T105265).
 *
 * But + is not safe because it's used to indicate a space; &= are only safe in
 * paths and not in queries (and we don't distinguish here); ' seems kind of
 * scary; and urlencode() doesn't touch -_. to begin with.  Plus, although /
 * is reserved, we don't care.  So the list we unescape is:
 *
 * ;:@$!*(),/~
 *
 * However, IIS7 redirects fail when the url contains a colon (see T24709),
 * so no fancy : for IIS7.
 *
 * %2F in the page titles seems to fatally break for some reason.
 *
 * @param string $s
 * @return string
 */
function wfUrlencode($s) {}
/**
 * This function takes one or two arrays as input, and returns a CGI-style string, e.g.
 * "days=7&limit=100". Options in the first array override options in the second.
 * Options set to null or false will not be output.
 *
 * @param array $array1 ( String|Array )
 * @param array|null $array2 ( String|Array )
 * @param string $prefix
 * @return string
 */
function wfArrayToCgi($array1, $array2 = null, $prefix = '') {}
/**
 * This is the logical opposite of wfArrayToCgi(): it accepts a query string as
 * its argument and returns the same string in array form.  This allows compatibility
 * with legacy functions that accept raw query strings instead of nice
 * arrays.  Of course, keys and values are urldecode()d.
 *
 * @param string $query Query string
 * @return string[] Array version of input
 */
function wfCgiToArray($query) {}
/**
 * Append a query string to an existing URL, which may or may not already
 * have query string parameters already. If so, they will be combined.
 *
 * @param string $url
 * @param string|array $query String or associative array
 * @return string
 */
function wfAppendQuery($url, $query) {}
/**
 * @deprecated since 1.43; get a UrlUtils from services, or construct your own
 * @internal
 * @return UrlUtils from services if initialized, otherwise make one from globals
 */
function wfGetUrlUtils(): \MediaWiki\Utils\UrlUtils {}
/**
 * Expand a potentially local URL to a fully-qualified URL using $wgServer
 * (or one of its alternatives).
 *
 * The meaning of the PROTO_* constants is as follows:
 * PROTO_HTTP: Output a URL starting with http://
 * PROTO_HTTPS: Output a URL starting with https://
 * PROTO_RELATIVE: Output a URL starting with // (protocol-relative URL)
 * PROTO_CURRENT: Output a URL starting with either http:// or https:// , depending
 *    on which protocol was used for the current incoming request
 * PROTO_CANONICAL: For URLs without a domain, like /w/index.php , use $wgCanonicalServer.
 *    For protocol-relative URLs, use the protocol of $wgCanonicalServer
 * PROTO_INTERNAL: Like PROTO_CANONICAL, but uses $wgInternalServer instead of $wgCanonicalServer
 *
 * If $url specifies a protocol, or $url is domain-relative and $wgServer
 * specifies a protocol, PROTO_HTTP, PROTO_HTTPS, PROTO_RELATIVE and
 * PROTO_CURRENT do not change that.
 *
 * Parent references (/../) in the path are resolved (as in UrlUtils::removeDotSegments()).
 *
 * @deprecated since 1.39, use UrlUtils::expand()
 * @param string $url An URL; can be absolute (e.g. http://example.com/foo/bar),
 *    protocol-relative (//example.com/foo/bar) or domain-relative (/foo/bar).
 * @param string|int|null $defaultProto One of the PROTO_* constants, as described above.
 * @return string|false Fully-qualified URL, current-path-relative URL or false if
 *    no valid URL can be constructed
 */
function wfExpandUrl($url, $defaultProto = PROTO_CURRENT) {}
/**
 * Get the wiki's "server", i.e. the protocol and host part of the URL, with a
 * protocol specified using a PROTO_* constant as in wfExpandUrl()
 *
 * @deprecated since 1.39, use UrlUtils::getServer(); hard-deprecated since 1.43
 * @since 1.32
 * @param string|int|null $proto One of the PROTO_* constants.
 * @return string The URL
 */
function wfGetServerUrl($proto) {}
/**
 * This function will reassemble a URL parsed with wfParseURL.  This is useful
 * if you need to edit part of a URL and put it back together.
 *
 * This is the basic structure used (brackets contain keys for $urlParts):
 * [scheme][delimiter][user]:[pass]@[host]:[port][path]?[query]#[fragment]
 *
 * @deprecated since 1.39, use UrlUtils::assemble()
 * @since 1.19
 * @param array $urlParts URL parts, as output from wfParseUrl
 * @return string URL assembled from its component parts
 */
function wfAssembleUrl($urlParts) {}
/**
 * Returns a partial regular expression of recognized URL protocols, e.g. "http:\/\/|https:\/\/"
 *
 * @deprecated since 1.39, use UrlUtils::validProtocols(); hard-deprecated since 1.43
 * @param bool $includeProtocolRelative If false, remove '//' from the returned protocol list.
 *        DO NOT USE this directly, use wfUrlProtocolsWithoutProtRel() instead
 * @return string
 */
function wfUrlProtocols($includeProtocolRelative = true) {}
/**
 * Like wfUrlProtocols(), but excludes '//' from the protocol list. Use this if
 * you need a regex that matches all URL protocols but does not match protocol-
 * relative URLs
 * @deprecated since 1.39, use UrlUtils::validAbsoluteProtocols()
 * @return string
 */
function wfUrlProtocolsWithoutProtRel() {}
/**
 * parse_url() work-alike, but non-broken.  Differences:
 *
 * 1) Handles protocols that don't use :// (e.g., mailto: and news:, as well as
 *    protocol-relative URLs) correctly.
 * 2) Adds a "delimiter" element to the array (see (2)).
 * 3) Verifies that the protocol is on the $wgUrlProtocols allowed list.
 * 4) Rejects some invalid URLs that parse_url doesn't, e.g. the empty string or URLs starting with
 *    a line feed character.
 *
 * @deprecated since 1.39, use UrlUtils::parse()
 * @param string $url A URL to parse
 * @return string[]|false Bits of the URL in an associative array, or false on failure.
 *   Possible fields:
 *   - scheme: URI scheme (protocol), e.g. 'http', 'mailto'. Lowercase, always present, but can
 *       be an empty string for protocol-relative URLs.
 *   - delimiter: either '://', ':' or '//'. Always present.
 *   - host: domain name / IP. Always present, but could be an empty string, e.g. for file: URLs.
 *   - port: port number. Will be missing when port is not explicitly specified.
 *   - user: user name, e.g. for HTTP Basic auth URLs such as http://user:pass@example.com/
 *       Missing when there is no username.
 *   - pass: password, same as above.
 *   - path: path including the leading /. Will be missing when empty (e.g. 'http://example.com')
 *   - query: query string (as a string; see wfCgiToArray() for parsing it), can be missing.
 *   - fragment: the part after #, can be missing.
 */
function wfParseUrl($url) {}
/**
 * Take a URL, make sure it's expanded to fully qualified, and replace any
 * encoded non-ASCII Unicode characters with their UTF-8 original forms
 * for more compact display and legibility for local audiences.
 *
 * @deprecated since 1.39, use UrlUtils::expandIRI(); hard-deprecated since 1.43
 * @param string $url
 * @return string
 */
function wfExpandIRI($url) {}
/**
 * Check whether a given URL has a domain that occurs in a given set of domains
 *
 * @deprecated since 1.39, use UrlUtils::expandIRI()
 * @param string $url
 * @param array $domains Array of domains (strings)
 * @return bool True if the host part of $url ends in one of the strings in $domains
 */
function wfMatchesDomainList($url, $domains) {}
/**
 * Sends a line to the debug log if enabled or, optionally, to a comment in output.
 * In normal operation this is a NOP.
 *
 * Controlling globals:
 * $wgDebugLogFile - points to the log file
 * $wgDebugRawPage - if false, 'action=raw' hits will not result in debug output.
 * $wgDebugComments - if on, some debug items may appear in comments in the HTML output.
 *
 * @since 1.25 support for additional context data
 *
 * @param string $text
 * @param string|bool $dest Destination of the message:
 *     - 'all': both to the log and HTML (debug toolbar or HTML comments)
 *     - 'private': excluded from HTML output
 *   For backward compatibility, it can also take a boolean:
 *     - true: same as 'all'
 *     - false: same as 'private'
 * @param array $context Additional logging context data
 */
function wfDebug($text, $dest = 'all', array $context = []) {}
/**
 * Returns true if debug logging should be suppressed if $wgDebugRawPage = false
 * @return bool
 */
function wfIsDebugRawPage() {}
/**
 * Send a line to a supplementary debug log file, if configured, or main debug
 * log if not.
 *
 * To configure a supplementary log file, set $wgDebugLogGroups[$logGroup] to
 * a string filename or an associative array mapping 'destination' to the
 * desired filename. The associative array may also contain a 'sample' key
 * with an integer value, specifying a sampling factor. Sampled log events
 * will be emitted with a 1 in N random chance.
 *
 * @since 1.23 support for sampling log messages via $wgDebugLogGroups.
 * @since 1.25 support for additional context data
 * @since 1.25 sample behavior dependent on configured $wgMWLoggerDefaultSpi
 *
 * @param string $logGroup
 * @param string $text
 * @param string|bool $dest Destination of the message:
 *     - 'all': both to the log and HTML (debug toolbar or HTML comments)
 *     - 'private': only to the specific log if set in $wgDebugLogGroups and
 *       discarded otherwise
 *   For backward compatibility, it can also take a boolean:
 *     - true: same as 'all'
 *     - false: same as 'private'
 * @param array $context Additional logging context data
 */
function wfDebugLog($logGroup, $text, $dest = 'all', array $context = []) {}
/**
 * Log for database errors
 *
 * @since 1.25 support for additional context data
 *
 * @param string $text Database error message.
 * @param array $context Additional logging context data
 */
function wfLogDBError($text, array $context = []) {}
/**
 * Logs a warning that a deprecated feature was used.
 *
 * To write a custom deprecation message, use wfDeprecatedMsg() instead.
 *
 * @param string $function Feature that is deprecated.
 * @param string|false $version Version of MediaWiki that the feature
 *  was deprecated in (Added in 1.19).
 * @param string|bool $component Component to which the feature belongs.
 *  If false, it is assumed the function is in MediaWiki core (Added in 1.19).
 * @param int $callerOffset How far up the call stack is the original
 *  caller. 2 = function that called the function that called
 *  wfDeprecated (Added in 1.20).
 * @throws InvalidArgumentException If the MediaWiki version
 *  number specified by $version is neither a string nor false.
 */
function wfDeprecated($function, $version = false, $component = false, $callerOffset = 2) {}
/**
 * Log a deprecation warning with arbitrary message text. A caller
 * description will be appended. If the message has already been sent for
 * this caller, it won't be sent again.
 *
 * Although there are component and version parameters, they are not
 * automatically appended to the message. The message text should include
 * information about when the thing was deprecated. The component and version
 * are just used to implement $wgDeprecationReleaseLimit.
 *
 * @since 1.35
 * @param string $msg The message
 * @param string|false $version Version of MediaWiki that the function
 *  was deprecated in.
 * @param string|bool $component Component to which the function belongs.
 *  If false, it is assumed the function is in MediaWiki core.
 * @param int|false $callerOffset How far up the call stack is the original
 *  caller. 2 = function that called the function that called us. If false,
 *  the caller description will not be appended.
 */
function wfDeprecatedMsg($msg, $version = false, $component = false, $callerOffset = 2) {}
/**
 * Send a warning either to the debug log or in a PHP error depending on
 * $wgDevelopmentWarnings. To log warnings in production, use wfLogWarning() instead.
 *
 * @param string $msg Message to send
 * @param int $callerOffset Number of items to go back in the backtrace to
 *        find the correct caller (1 = function calling wfWarn, ...)
 * @param int $level PHP error level; defaults to E_USER_NOTICE;
 *        only used when $wgDevelopmentWarnings is true
 */
function wfWarn($msg, $callerOffset = 1, $level = E_USER_NOTICE) {}
/**
 * Send a warning as a PHP error and the debug log. This is intended for logging
 * warnings in production. For logging development warnings, use WfWarn instead.
 *
 * @param string $msg Message to send
 * @param int $callerOffset Number of items to go back in the backtrace to
 *        find the correct caller (1 = function calling wfLogWarning, ...)
 * @param int $level PHP error level; defaults to E_USER_WARNING
 */
function wfLogWarning($msg, $callerOffset = 1, $level = E_USER_WARNING) {}
/**
 * This is the function for getting translated interface messages.
 *
 * @see Message class for documentation how to use them.
 * @see https://www.mediawiki.org/wiki/Manual:Messages_API
 *
 * This function replaces all old wfMsg* functions.
 *
 * When the MessageSpecifier object is an instance of Message, a clone of the object is returned.
 * This is unlike the `new Message( … )` constructor, which returns a new object constructed from
 * scratch with the same key. This difference is mostly relevant when the passed object is an
 * instance of a subclass like RawMessage or ApiMessage.
 *
 * @param string|string[]|MessageSpecifier $key Message key, or array of keys, or a MessageSpecifier
 * @param mixed ...$params Normal message parameters
 * @return Message
 *
 * @since 1.17
 *
 * @see Message::__construct
 */
function wfMessage($key, ...$params) {}
/**
 * This function accepts multiple message keys and returns a message instance
 * for the first message which is non-empty. If all messages are empty then an
 * instance of the last message key is returned.
 *
 * @param string ...$keys Message keys
 * @return Message
 *
 * @since 1.18
 *
 * @see Message::newFallbackSequence
 */
function wfMessageFallback(...$keys) {}
/**
 * Replace message parameter keys on the given formatted output.
 *
 * @param string $message
 * @param array $args
 * @return string
 * @internal
 */
function wfMsgReplaceArgs($message, $args) {}
/**
 * Get host name of the current machine, for use in error reporting.
 *
 * This helps to know which machine in a data center generated the
 * current page.
 *
 * @return string
 */
function wfHostname() {}
/**
 * Safety wrapper for debug_backtrace().
 *
 * Will return an empty array if debug_backtrace is disabled, otherwise
 * the output from debug_backtrace() (trimmed).
 *
 * @param int $limit This parameter can be used to limit the number of stack frames returned
 *
 * @return array Array of backtrace information
 */
function wfDebugBacktrace($limit = 0) {}
/**
 * Get a debug backtrace as a string
 *
 * @param bool|null $raw If true, the return value is plain text. If false, HTML.
 *   Defaults to true if MW_ENTRY_POINT is 'cli', otherwise false.
 * @return string
 * @since 1.25 Supports $raw parameter.
 */
function wfBacktrace($raw = null) {}
/**
 * Get the name of the function which called this function
 * wfGetCaller( 1 ) is the function with the wfGetCaller() call (ie. __FUNCTION__)
 * wfGetCaller( 2 ) [default] is the caller of the function running wfGetCaller()
 * wfGetCaller( 3 ) is the parent of that.
 *
 * The format will be the same as for {@see wfFormatStackFrame()}.
 * @param int $level
 * @return string function name or 'unknown'
 */
function wfGetCaller($level = 2) {}
/**
 * Return a string consisting of callers in the stack. Useful sometimes
 * for profiling specific points.
 *
 * @param int|false $limit The maximum depth of the stack frame to return, or false for the entire stack.
 * @return string
 */
function wfGetAllCallers($limit = 3) {}
/**
 * Return a string representation of frame
 *
 * Typically, the returned value will be in one of these formats:
 * - method
 * - Fully\Qualified\method
 * - Fully\Qualified\Class->method
 * - Fully\Qualified\Class::method
 *
 * @param array $frame
 * @return string
 */
function wfFormatStackFrame($frame) {}
/**
 * Whether the client accept gzip encoding
 *
 * Uses the Accept-Encoding header to check if the client supports gzip encoding.
 * Use this when considering to send a gzip-encoded response to the client.
 *
 * @param bool $force Forces another check even if we already have a cached result.
 * @return bool
 */
function wfClientAcceptsGzip($force = false) {}
/**
 * Escapes the given text so that it may be output using addWikiText()
 * without any linking, formatting, etc. making its way through. This
 * is achieved by substituting certain characters with HTML entities.
 * As required by the callers, "<nowiki>" is not used.
 *
 * @param string|null|false $input Text to be escaped
 * @param-taint $input escapes_html
 * @return string
 */
function wfEscapeWikiText($input): string {}
/**
 * Sets dest to source and returns the original value of dest
 * If source is NULL, it just returns the value, it doesn't set the variable
 * If force is true, it will set the value even if source is NULL
 *
 * @param mixed &$dest
 * @param mixed $source
 * @param bool $force
 * @return mixed
 */
function wfSetVar(&$dest, $source, $force = false) {}
/**
 * As for wfSetVar except setting a bit
 *
 * @param int &$dest
 * @param int $bit
 * @param bool $state
 *
 * @return bool
 */
function wfSetBit(&$dest, $bit, $state = true) {}
/**
 * A wrapper around the PHP function var_export().
 * Either print it or add it to the regular output ($wgOut).
 *
 * @param mixed $var A PHP variable to dump.
 */
function wfVarDump($var) {}
/**
 * Provide a simple HTTP error.
 *
 * @param int|string $code
 * @param string $label
 * @param string $desc
 */
function wfHttpError($code, $label, $desc) {}
/**
 * Clear away any user-level output buffers, discarding contents.
 *
 * Suitable for 'starting afresh', for instance when streaming
 * relatively large amounts of data without buffering, or wanting to
 * output image files without ob_gzhandler's compression.
 *
 * The optional $resetGzipEncoding parameter controls suppression of
 * the Content-Encoding header sent by ob_gzhandler; by default it
 * is left. This should be used for HTTP 304 responses, where you need to
 * preserve the Content-Encoding header of the real result, but
 * also need to suppress the output of ob_gzhandler to keep to spec
 * and avoid breaking Firefox in rare cases where the headers and
 * body are broken over two packets.
 *
 * Note that some PHP configuration options may add output buffer
 * layers which cannot be removed; these are left in place.
 *
 * @param bool $resetGzipEncoding
 */
function wfResetOutputBuffers($resetGzipEncoding = true) {}
/**
 * Get a timestamp string in one of various formats
 *
 * @param mixed $outputtype Output format, one of the TS_* constants. Defaults to
 *   Unix timestamp.
 * @param mixed $ts A timestamp in any supported format. The
 *   function will autodetect which format is supplied and act accordingly. Use 0 or
 *   omit to use current time
 * @return string|false The date in the specified format, or false on error.
 */
function wfTimestamp($outputtype = TS_UNIX, $ts = 0) {}
/**
 * Return a formatted timestamp, or null if input is null.
 * For dealing with nullable timestamp columns in the database.
 *
 * @param mixed $outputtype
 * @param mixed|null $ts
 * @return string|false|null Null if called with null, otherwise the result of wfTimestamp()
 */
function wfTimestampOrNull($outputtype = TS_UNIX, $ts = null) {}
/**
 * Convenience function; returns MediaWiki timestamp for the present time.
 *
 * @return string TS_MW timestamp
 */
function wfTimestampNow() {}
/**
 * Tries to get the system directory for temporary files. First
 * $wgTmpDirectory is checked, and then the TMPDIR, TMP, and TEMP
 * environment variables are then checked in sequence, then
 * sys_get_temp_dir(), then upload_tmp_dir from php.ini.
 *
 * NOTE: When possible, use instead the tmpfile() function to create
 * temporary files to avoid race conditions on file creation, etc.
 *
 * @return string
 */
function wfTempDir() {}
/**
 * Make directory, and make all parent directories if they don't exist
 *
 * @param string $dir Full path to directory to create. Callers should make sure this is not a storage path.
 * @param int|null $mode Chmod value to use, default is $wgDirectoryMode
 * @param string|null $caller Optional caller param for debugging.
 * @return bool
 */
function wfMkdirParents($dir, $mode = null, $caller = null) {}
/**
 * Remove a directory and all its content.
 * Does not hide error.
 * @param string $dir
 */
function wfRecursiveRemoveDir($dir) {}
/**
 * @param float|int $nr The number to format
 * @param int $acc The number of digits after the decimal point, default 2
 * @param bool $round Whether or not to round the value, default true
 * @return string
 */
function wfPercent($nr, int $acc = 2, bool $round = true) {}
/**
 * Safety wrapper around ini_get() for boolean settings.
 * The values returned from ini_get() are pre-normalized for settings
 * set via php.ini or php_flag/php_admin_flag... but *not*
 * for those set via php_value/php_admin_value.
 *
 * It's fairly common for people to use php_value instead of php_flag,
 * which can leave you with an 'off' setting giving a false positive
 * for code that just takes the ini_get() return value as a boolean.
 *
 * To make things extra interesting, setting via php_value accepts
 * "true" and "yes" as true, but php.ini and php_flag consider them false. :)
 * Unrecognized values go false... again opposite PHP's own coercion
 * from string to bool.
 *
 * Luckily, 'properly' set settings will always come back as '0' or '1',
 * so we only have to worry about them and the 'improper' settings.
 *
 * I frickin' hate PHP... :P
 *
 * @param string $setting
 * @return bool
 */
function wfIniGetBool($setting) {}
/**
 * Convert string value to boolean, when the following are interpreted as true:
 * - on
 * - true
 * - yes
 * - Any number, except 0
 * All other strings are interpreted as false.
 *
 * @param string $val
 * @return bool
 * @since 1.31
 */
function wfStringToBool($val) {}
/**
 * Locale-independent version of escapeshellarg()
 *
 * Originally, this fixed the incorrect use of single quotes on Windows
 * (https://bugs.php.net/bug.php?id=26285) and the locale problems on Linux in
 * PHP 5.2.6+ (https://bugs.php.net/bug.php?id=54391). The second bug is still
 * open as of 2021.
 *
 * @param string|string[] ...$args strings to escape and glue together,
 *  or a single array of strings parameter
 * @return string
 * @deprecated since 1.30 use MediaWiki\Shell\Shell::escape()
 */
function wfEscapeShellArg(...$args) {}
/**
 * Execute a shell command, with time and memory limits mirrored from the PHP
 * configuration if supported.
 *
 * @param string|string[] $cmd If string, a properly shell-escaped command line,
 *   or an array of unescaped arguments, in which case each value will be escaped
 *   Example:   [ 'convert', '-font', 'font name' ] would produce "'convert' '-font' 'font name'"
 * @param null|mixed &$retval Optional, will receive the program's exit code.
 *   (non-zero is usually failure). If there is an error from
 *   read, select, or proc_open(), this will be set to -1.
 * @param array $environ Optional environment variables which should be
 *   added to the executed command environment.
 * @param array $limits Optional array with limits(filesize, memory, time, walltime)
 *   this overwrites the global wgMaxShell* limits.
 * @param array $options Array of options:
 *   - duplicateStderr: Set this to true to duplicate stderr to stdout,
 *     including errors from limit.sh
 *   - profileMethod: By default this function will profile based on the calling
 *     method. Set this to a string for an alternative method to profile from
 * @phan-param array{duplicateStderr?:bool,profileMethod?:string} $options
 *
 * @return string Collected stdout as a string
 * @deprecated since 1.30 use class MediaWiki\Shell\Shell
 */
function wfShellExec($cmd, &$retval = null, $environ = [], $limits = [], $options = []) {}
/**
 * Execute a shell command, returning both stdout and stderr. Convenience
 * function, as all the arguments to wfShellExec can become unwieldy.
 *
 * @note This also includes errors from limit.sh, e.g. if $wgMaxShellFileSize is exceeded.
 * @param string|string[] $cmd If string, a properly shell-escaped command line,
 *   or an array of unescaped arguments, in which case each value will be escaped
 *   Example:   [ 'convert', '-font', 'font name' ] would produce "'convert' '-font' 'font name'"
 * @param null|mixed &$retval Optional, will receive the program's exit code.
 *   (non-zero is usually failure)
 * @param array $environ Optional environment variables which should be
 *   added to the executed command environment.
 * @param array $limits Optional array with limits(filesize, memory, time, walltime)
 *   this overwrites the global wgMaxShell* limits.
 * @return string Collected stdout and stderr as a string
 * @deprecated since 1.30 use class MediaWiki\Shell\Shell
 */
function wfShellExecWithStderr($cmd, &$retval = null, $environ = [], $limits = []) {}
/**
 * Generate a shell-escaped command line string to run a MediaWiki cli script.
 * Note that $parameters should be a flat array and an option with an argument
 * should consist of two consecutive items in the array (do not use "--option value").
 *
 * @deprecated since 1.31, use Shell::makeScriptCommand()
 *
 * @param string $script MediaWiki cli script path
 * @param array $parameters Arguments and options to the script
 * @param array $options Associative array of options:
 *     'php': The path to the php executable
 *     'wrapper': Path to a PHP wrapper to handle the maintenance script
 * @phan-param array{php?:string,wrapper?:string} $options
 * @return string
 */
function wfShellWikiCmd($script, array $parameters = [], array $options = []) {}
/**
 * wfMerge attempts to merge differences between three texts.
 *
 * @param string $old Common base revision
 * @param string $mine The edit we wish to store but which potentially conflicts with another edit
 *     which happened since we started editing.
 * @param string $yours The most recent stored revision of the article. Note that "mine" and "yours"
 *     might have another meaning depending on the specific use case.
 * @param string|null &$simplisticMergeAttempt Automatically merged text, with overlapping edits
 *     falling back to "my" text.
 * @param string|null &$mergeLeftovers Optional out parameter containing an "ed" script with the
 *     remaining bits of "your" text that could not be merged into $simplisticMergeAttempt.
 *     The "ed" file format is documented here:
 *     https://www.gnu.org/software/diffutils/manual/html_node/Detailed-ed.html
 * @return bool true for a clean merge and false for failure or a conflict.
 */
function wfMerge(string $old, string $mine, string $yours, ?string &$simplisticMergeAttempt, ?string &$mergeLeftovers = null): bool {}
/**
 * Return the final portion of a pathname.
 * Reimplemented because PHP5's "basename()" is buggy with multibyte text.
 * https://bugs.php.net/bug.php?id=33898
 *
 * PHP's basename() only considers '\' a pathchar on Windows and Netware.
 * We'll consider it so always, as we don't want '\s' in our Unix paths either.
 *
 * @param string $path
 * @param string $suffix String to remove if present
 * @return string
 */
function wfBaseName($path, $suffix = '') {}
/**
 * Generate a relative path name to the given file.
 * May explode on non-matching case-insensitive paths,
 * funky symlinks, etc.
 *
 * @param string $path Absolute destination path including target filename
 * @param string $from Absolute source path, directory only
 * @return string
 */
function wfRelativePath($path, $from) {}
/**
 * Get a Database object.
 *
 * @param int $db Index of the connection to get. May be DB_PRIMARY for the
 *            primary (for write queries), DB_REPLICA for potentially lagged read
 *            queries, or an integer >= 0 for a particular server.
 *
 * @param string|string[] $groups Query groups. An array of group names that this query
 *                belongs to. May contain a single string if the query is only
 *                in one group.
 *
 * @param string|false $wiki The wiki ID, or false for the current wiki
 *
 * Note: multiple calls to wfGetDB(DB_REPLICA) during the course of one request
 * will always return the same object, unless the underlying connection or load
 * balancer is manually destroyed.
 *
 * Note 2: use $this->getDB() in maintenance scripts that may be invoked by
 * updater to ensure that a proper database is being updated.
 *
 * Note 3: When replacing calls to this with calls to methods on an injected
 * LoadBalancer, LoadBalancer::getConnection is more commonly needed than
 * LoadBalancer::getMaintenanceConnectionRef, which is needed for more advanced
 * administrative tasks. See the IMaintainableDatabase and IDatabase interfaces
 * for details.
 *
 * @deprecated since 1.39, emitting warnings since 1.42; instead, you can use:
 *   $services = MediaWikiServices::getInstance();
 *   $dbr = $services->getConnectionProvider()->getReplicaDatabase();
 *   $dbw = $services->getConnectionProvider()->getPrimaryDatabase();
 *
 * 	 … or, in rare circumstances, you may need to use:
 *
 *   $services->getDBLoadBalancer()->getConnection() / getMaintenanceConnectionRef()
 *
 * @return \Wikimedia\Rdbms\DBConnRef
 */
function wfGetDB($db, $groups = [], $wiki = false) {}
/**
 * Get the URL path to a MediaWiki entry point.
 *
 * This is a wrapper to respect $wgScript and $wgLoadScript overrides.
 *
 * @see MW_ENTRY_POINT
 * @param string $script Name of entrypoint, without `.php` extension.
 * @return string
 */
function wfScript($script = 'index') {}
/**
 * Convenience function converts boolean values into "true"
 * or "false" (string) values
 *
 * @param bool $value
 * @return string
 */
function wfBoolToStr($value) {}
/**
 * Get a platform-independent path to the null file, e.g. /dev/null
 *
 * @return string
 */
function wfGetNull() {}
/**
 * Replace all invalid characters with '-'.
 * Additional characters can be defined in $wgIllegalFileChars (see T22489).
 * By default, $wgIllegalFileChars includes ':', '/', '\'.
 *
 * @param string $name Filename to process
 * @return string
 */
function wfStripIllegalFilenameChars($name) {}
/**
 * Raise PHP's memory limit (if needed).
 *
 * @internal For use by Setup.php
 * @param int $newLimit
 */
function wfMemoryLimit($newLimit) {}
/**
 * Raise the request time limit to $wgTransactionalTimeLimit
 *
 * @return int Prior time limit
 * @since 1.26
 */
function wfTransactionalTimeLimit() {}
/**
 * Converts shorthand byte notation to integer form
 *
 * @param null|string $string
 * @param int $default Returned if $string is empty
 * @return int
 */
function wfShorthandToInteger(?string $string = '', int $default = -1): int {}
/**
 * Determine input string is represents as infinity
 *
 * @param string $str The string to determine
 * @return bool
 * @since 1.25
 */
function wfIsInfinity($str) {}
/**
 * Returns true if these thumbnail parameters match one that MediaWiki
 * requests from file description pages and/or parser output.
 *
 * $params is considered non-standard if they involve a non-standard
 * width or any non-default parameters aside from width and page number.
 * The number of possible files with standard parameters is far less than
 * that of all combinations; rate-limiting for them can thus be more generous.
 *
 * @param File $file
 * @param array $params
 * @return bool
 * @since 1.24 Moved from thumb.php to GlobalFunctions in 1.25
 */
function wfThumbIsStandard(\File $file, array $params) {}
/**
 * Merges two (possibly) 2 dimensional arrays into the target array ($baseArray).
 *
 * Values that exist in both values will be combined with += (all values of the array
 * of $newValues will be added to the values of the array of $baseArray, while values,
 * that exists in both, the value of $baseArray will be used).
 *
 * @param array $baseArray The array where you want to add the values of $newValues to
 * @param array $newValues An array with new values
 * @return array The combined array
 * @since 1.26
 */
function wfArrayPlus2d(array $baseArray, array $newValues) {}
define('IPPROTO_IP', 0);
define('IP_MULTICAST_LOOP', 34);
define('IP_MULTICAST_TTL', 33);
define('MW_VERSION', '1.44.0-alpha');
define('LIST_COMMA', IDatabase::LIST_COMMA);
define('LIST_AND', IDatabase::LIST_AND);
define('LIST_SET', IDatabase::LIST_SET);
define('LIST_NAMES', IDatabase::LIST_NAMES);
define('LIST_OR', IDatabase::LIST_OR);
define('NS_MEDIA', -2);
define('NS_SPECIAL', -1);
define('NS_MAIN', 0);
define('NS_TALK', 1);
define('NS_USER', 2);
define('NS_USER_TALK', 3);
define('NS_PROJECT', 4);
define('NS_PROJECT_TALK', 5);
define('NS_FILE', 6);
define('NS_FILE_TALK', 7);
define('NS_MEDIAWIKI', 8);
define('NS_MEDIAWIKI_TALK', 9);
define('NS_TEMPLATE', 10);
define('NS_TEMPLATE_TALK', 11);
define('NS_HELP', 12);
define('NS_HELP_TALK', 13);
define('NS_CATEGORY', 14);
define('NS_CATEGORY_TALK', 15);
define('CACHE_ANYTHING', -1);
define('CACHE_NONE', 0);
define('CACHE_DB', 1);
define('CACHE_MEMCACHED', 'memcached-php');
define('CACHE_ACCEL', 3);
define('CACHE_HASH', 'hash');
define('AV_NO_VIRUS', 0);
define('AV_VIRUS_FOUND', 1);
define('AV_SCAN_ABORTED', -1);
define('AV_SCAN_FAILED', false);
define('MW_DATE_DEFAULT', 'default');
define('MW_DATE_MDY', 'mdy');
define('MW_DATE_DMY', 'dmy');
define('MW_DATE_YMD', 'ymd');
define('MW_DATE_ISO', 'ISO 8601');
define('RC_EDIT', 0);
define('RC_NEW', 1);
define('RC_LOG', 3);
define('RC_EXTERNAL', 5);
define('RC_CATEGORIZE', 6);
define('EDIT_NEW', 1);
define('EDIT_UPDATE', 2);
define('EDIT_MINOR', 4);
define('EDIT_SUPPRESS_RC', 8);
define('EDIT_FORCE_BOT', 16);
define('EDIT_DEFER_UPDATES', 32);
define('EDIT_AUTOSUMMARY', 64);
define('EDIT_INTERNAL', 128);
define('MW_SUPPORTS_PARSERFIRSTCALLINIT', 1);
define('MW_SUPPORTS_LOCALISATIONCACHE', 1);
define('MW_SUPPORTS_CONTENTHANDLER', 1);
define('MW_EDITFILTERMERGED_SUPPORTS_API', 1);
define('MW_SUPPORTS_RESOURCE_MODULES', 1);
define('OT_HTML', 1);
define('OT_WIKI', 2);
define('OT_PREPROCESS', 3);
define('OT_PLAIN', 4);
define('SFH_NO_HASH', 1);
define('SFH_OBJECT_ARGS', 2);
define('APCOND_EDITCOUNT', 1);
define('APCOND_AGE', 2);
define('APCOND_EMAILCONFIRMED', 3);
define('APCOND_INGROUPS', 4);
define('APCOND_ISIP', 5);
define('APCOND_IPINRANGE', 6);
define('APCOND_AGE_FROM_EDIT', 7);
define('APCOND_BLOCKED', 8);
define('APCOND_ISBOT', 9);
define('CUDCOND_AFTER', 'registered-after');
define('CUDCOND_ANON', 'anonymous-user');
define('CUDCOND_NAMED', 'named-user');
define('CUDCOND_USERGROUP', 'usergroup');
define('PROTO_HTTP', 'http://');
define('PROTO_HTTPS', 'https://');
define('PROTO_RELATIVE', '//');
define('PROTO_FALLBACK', null);
define('PROTO_CURRENT', PROTO_FALLBACK);
define('PROTO_CANONICAL', 1);
define('PROTO_INTERNAL', 2);
define('CONTENT_MODEL_WIKITEXT', 'wikitext');
define('CONTENT_MODEL_JAVASCRIPT', 'javascript');
define('CONTENT_MODEL_CSS', 'css');
define('CONTENT_MODEL_TEXT', 'text');
define('CONTENT_MODEL_JSON', 'json');
define('CONTENT_MODEL_UNKNOWN', 'unknown');
define('CONTENT_FORMAT_WIKITEXT', 'text/x-wiki');
define('CONTENT_FORMAT_JAVASCRIPT', 'text/javascript');
define('CONTENT_FORMAT_CSS', 'text/css');
define('CONTENT_FORMAT_TEXT', 'text/plain');
define('CONTENT_FORMAT_HTML', 'text/html');
define('CONTENT_FORMAT_SERIALIZED', 'application/vnd.php.serialized');
define('CONTENT_FORMAT_JSON', 'application/json');
define('CONTENT_FORMAT_XML', 'application/xml');
define('SHELL_MAX_ARG_STRLEN', '100000');
define('SCHEMA_COMPAT_WRITE_OLD', 0x1);
define('SCHEMA_COMPAT_READ_OLD', 0x2);
define('SCHEMA_COMPAT_WRITE_TEMP', 0x10);
define('SCHEMA_COMPAT_READ_TEMP', 0x20);
define('SCHEMA_COMPAT_WRITE_NEW', 0x100);
define('SCHEMA_COMPAT_READ_NEW', 0x200);
define('SCHEMA_COMPAT_WRITE_MASK', SCHEMA_COMPAT_WRITE_OLD | SCHEMA_COMPAT_WRITE_TEMP | SCHEMA_COMPAT_WRITE_NEW);
define('SCHEMA_COMPAT_READ_MASK', SCHEMA_COMPAT_READ_OLD | SCHEMA_COMPAT_READ_TEMP | SCHEMA_COMPAT_READ_NEW);
define('SCHEMA_COMPAT_WRITE_BOTH', SCHEMA_COMPAT_WRITE_OLD | SCHEMA_COMPAT_WRITE_NEW);
define('SCHEMA_COMPAT_WRITE_OLD_AND_TEMP', SCHEMA_COMPAT_WRITE_OLD | SCHEMA_COMPAT_WRITE_TEMP);
define('SCHEMA_COMPAT_WRITE_TEMP_AND_NEW', SCHEMA_COMPAT_WRITE_TEMP | SCHEMA_COMPAT_WRITE_NEW);
define('SCHEMA_COMPAT_READ_BOTH', SCHEMA_COMPAT_READ_OLD | SCHEMA_COMPAT_READ_NEW);
define('SCHEMA_COMPAT_OLD', SCHEMA_COMPAT_WRITE_OLD | SCHEMA_COMPAT_READ_OLD);
define('SCHEMA_COMPAT_TEMP', SCHEMA_COMPAT_WRITE_TEMP | SCHEMA_COMPAT_READ_TEMP);
define('SCHEMA_COMPAT_NEW', SCHEMA_COMPAT_WRITE_NEW | SCHEMA_COMPAT_READ_NEW);
define('MIGRATION_OLD', 0x0 | SCHEMA_COMPAT_OLD);
define('MIGRATION_WRITE_BOTH', 0x10000000 | SCHEMA_COMPAT_READ_BOTH | SCHEMA_COMPAT_WRITE_BOTH);
define('MIGRATION_WRITE_NEW', 0x20000000 | SCHEMA_COMPAT_READ_BOTH | SCHEMA_COMPAT_WRITE_NEW);
define('MIGRATION_NEW', 0x30000000 | SCHEMA_COMPAT_NEW);
define('XML_DUMP_SCHEMA_VERSION_10', '0.10');
define('XML_DUMP_SCHEMA_VERSION_11', '0.11');
/**
 * Functions that need to be available during bootstrapping.
 * Code in this file cannot expect MediaWiki to have been initialized.
 * @file
 */
/**
 * Decide and remember where to load LocalSettings from.
 *
 * This is used by Setup.php and will (if not already) store the result
 * in the MW_CONFIG_FILE constant.
 *
 * The primary settings file is traditionally LocalSettings.php under the %MediaWiki
 * installation path, but can also be placed differently and specified via the
 * MW_CONFIG_FILE constant (from an entrypoint wrapper) or via a `MW_CONFIG_FILE`
 * environment variable (from the web server).
 *
 * Experimental: The settings file can use the `.yaml` or `.json` extension, which
 * must use the format described on
 * https://www.mediawiki.org/wiki/Manual:YAML_settings_file_format.
 *
 * @internal Only for use by Setup.php and Installer.
 * @since 1.38
 * @param string|null $installationPath The installation's base path,
 *        as returned by wfDetectInstallPath().
 *
 * @return string The path to the settings file
 */
function wfDetectLocalSettingsFile(?string $installationPath = null): string {}
/**
 * Decide and remember where mediawiki is installed.
 *
 * This is used by Setup.php and will (if not already) store the result
 * in the MW_INSTALL_PATH constant.
 *
 * The install path is detected based on the location of this file,
 * but can be overwritten using the MW_INSTALL_PATH environment variable.
 *
 * @internal Only for use by Setup.php and Installer.
 * @since 1.39
 * @return string The path to the mediawiki installation
 */
function wfDetectInstallPath(): string {}
/**
 * Check if the operating system is Windows
 *
 * @return bool True if it's Windows, false otherwise.
 */
function wfIsWindows() {}
/**
 * Check if we are running from the commandline
 *
 * @since 1.31
 * @return bool
 */
function wfIsCLI() {}
define('MSG_CACHE_VERSION', 2);
/**
 * @param SettingsBuilder $settings
 * @return never
 */
function wfWebStartNoLocalSettings(\MediaWiki\Settings\SettingsBuilder $settings) {}
function wfWebStartSetup(\MediaWiki\Settings\SettingsBuilder $settings) {}
define('MW_SETUP_CALLBACK', 'wfWebStartSetup');
/**
 * Check PHP version and that external dependencies are installed, and
 * display an informative error if either condition is not satisfied.
 *
 * @param string $format One of "text" or "html"
 * @param string $scriptPath Used when an error is formatted as HTML.
 */
function wfEntryPointCheck($format = 'text', $scriptPath = '/') {}