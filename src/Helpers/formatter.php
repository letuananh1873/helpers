<?php
if( ! function_exists( 'lara_json_validate' ) ) {
    function lara_json_validate( $array ) {
        $attr = array();
        if( ! empty($array) ) {
            $attr[] = sprintf( 'data-validate="%s"', htmlspecialchars( json_encode($array) ) );
            
            if( isset($array['required']) ) {
                $attr[] = 'required';
            }
        }
        
        return implode(' ', $attr);
    }
}

function lara_parse_args( $args, $defaults = '' ) {
    if ( is_object( $args ) ) {
        $r = get_object_vars( $args );
    } elseif ( is_array( $args ) ) {
        $r =& $args;
    } else {
        wp_parse_str( $args, $r );
    }
 
    if ( is_array( $defaults ) ) {
        return array_merge( $defaults, $r );
    }
    return $r;
}
if (!function_exists('get_domain_name')) {
    function get_domain_name($url, $scheme = true) {
        if( $url ) {
            $result = parse_url($url);

            if( $scheme ) {
                return rtrim( $result['scheme']."://".$result['host'], '/' );
            }else {
                return $result['host'];
            }
            
        }
    }
}

function is_serialized( $data, $strict = true ) {
    // if it isn't a string, it isn't serialized.
    if ( ! is_string( $data ) ) {
        return false;
    }
    $data = trim( $data );
    if ( 'N;' == $data ) {
        return true;
    }
    if ( strlen( $data ) < 4 ) {
        return false;
    }
    if ( ':' !== $data[1] ) {
        return false;
    }
    if ( $strict ) {
        $lastc = substr( $data, -1 );
        if ( ';' !== $lastc && '}' !== $lastc ) {
            return false;
        }
    } else {
        $semicolon = strpos( $data, ';' );
        $brace     = strpos( $data, '}' );
        // Either ; or } must exist.
        if ( false === $semicolon && false === $brace ) {
            return false;
        }
        // But neither must be in the first X characters.
        if ( false !== $semicolon && $semicolon < 3 ) {
            return false;
        }
        if ( false !== $brace && $brace < 4 ) {
            return false;
        }
    }
    $token = $data[0];
    switch ( $token ) {
        case 's':
            if ( $strict ) {
                if ( '"' !== substr( $data, -2, 1 ) ) {
                    return false;
                }
            } elseif ( false === strpos( $data, '"' ) ) {
                return false;
            }
            // or else fall through
        case 'a':
        case 'O':
            return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
    }
    return false;
}

if (!function_exists('phone')) {

    /**
     * phone formatter
     *
     * @param string $phone
     * @return string
     */
    function phone($phone)
    {
        //do we have an extension?

        //strip out non-numerics
        $phone = preg_replace("/[^0-9]/", "", $phone);

        //area code
        if (strlen($phone) == 10) {
            $phone = '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
        }

        //no area code
        elseif (strlen($phone) == 7) {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 4);
        }

        //return
        return $phone;
    }
}

if (!function_exists('blurb')) {

    /**
     * blurb
     *
     * @param string  $blurb
     * @param integer $maxChars
     * @param string  $suffix
     * @param bool    $br
     * @return string
     */
    function blurb($blurb, $maxChars = null, $suffix = '...', $br = true)
    {
        //blurb is shorter than max chars
        if (strlen($blurb) < $maxChars) {
            return nl2br($blurb);
        }

        //shorten output
        if ($maxChars) {
            $blurb = wordwrap($blurb, $maxChars, '<>');
            $blurb = explode('<>', $blurb);
            $blurb = $blurb[0];
        }

        //full output with line breaks
        if ($br) {
            $blurb = nl2br($blurb);
        }

        //return
        return $blurb . $suffix;
    }
}

if (!function_exists('formatBytes')) {

    /**
     * format bytes into something human readable
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        //calculate bytes
        $bytes /= pow(1024, $pow);

        //return the bytes
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if ( ! function_exists('lara_clean') ) {
    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if ( ! function_exists('lara_clean') ) {
    function lara_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'lara_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_fields( $var ) : $var;
        }
    }
}

/**
 * Internal helper function to sanitize a string from user input or from the db
 *
 * @since 4.7.0
 * @access private
 *
 * @param string $str String to sanitize.
 * @param bool $keep_newlines optional Whether to keep newlines. Default: false.
 * @return string Sanitized string.
 */
if ( ! function_exists('sanitize_text_fields') ) {
    function sanitize_text_fields( $str, $keep_newlines = false ) {
        if ( is_object( $str ) || is_array( $str ) ) {
            return '';
        }
     
        $str = (string) $str;
     
        $filtered = lara_check_invalid_utf8( $str );
     
        if ( strpos( $filtered, '<' ) !== false ) {
            $filtered = lara_pre_kses_less_than( $filtered );
            // This will strip extra whitespace for us.
            $filtered = lara_strip_all_tags( $filtered, false );
     
            // Use html entities in a special case to make sure no later
            // newline stripping stage could lead to a functional tag
            $filtered = str_replace( "<\n", "&lt;\n", $filtered );
        }
     
        if ( ! $keep_newlines ) {
            $filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
        }
        $filtered = trim( $filtered );
     
        $found = false;
        while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
            $filtered = str_replace( $match[0], '', $filtered );
            $found    = true;
        }
     
        if ( $found ) {
            // Strip out the whitespace that may now exist after removing the octets.
            $filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
        }
     
        return $filtered;
    }
}

/**
 * Checks for invalid UTF8 in a string.
 *
 * @since 2.8.0
 *
 * @staticvar bool $is_utf8
 * @staticvar bool $utf8_pcre
 *
 * @param string  $string The text which is to be checked.
 * @param bool    $strip Optional. Whether to attempt to strip out invalid UTF8. Default is false.
 * @return string The checked text.
 */
function lara_check_invalid_utf8( $string, $strip = false ) {
    $string = (string) $string;
 
    if ( 0 === strlen( $string ) ) {
        return '';
    }
 
    // Store the site charset as a static to avoid multiple calls to get_option()
    static $is_utf8 = null;
    if ( ! isset( $is_utf8 ) ) {
        $is_utf8 = in_array( 'UTF-8', array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) );
    }
    if ( ! $is_utf8 ) {
        return $string;
    }
 
    // Check for support for utf8 in the installed PCRE library once and store the result in a static
    static $utf8_pcre = null;
    if ( ! isset( $utf8_pcre ) ) {
        $utf8_pcre = @preg_match( '/^./u', 'a' );
    }
    // We can't demand utf8 in the PCRE installation, so just return the string in those cases
    if ( ! $utf8_pcre ) {
        return $string;
    }
 
    // preg_match fails when it encounters invalid UTF8 in $string
    if ( 1 === @preg_match( '/^./us', $string ) ) {
        return $string;
    }
 
    // Attempt to strip the bad chars if requested (not recommended)
    if ( $strip && function_exists( 'iconv' ) ) {
        return iconv( 'utf-8', 'utf-8', $string );
    }
 
    return '';
}

/**
 * Convert lone less than signs.
 *
 * KSES already converts lone greater than signs.
 *
 * @since 2.3.0
 *
 * @param string $text Text to be converted.
 * @return string Converted text.
 */
function lara_pre_kses_less_than( $text ) {
    return preg_replace_callback( '%<[^>]*?((?=<)|>|$)%', 'lara_pre_kses_less_than_callback', $text );
}

/**
 * Callback function used by preg_replace.
 *
 * @since 2.3.0
 *
 * @param array $matches Populated by matches to preg_replace.
 * @return string The text returned after esc_html if needed.
 */
function lara_pre_kses_less_than_callback( $matches ) {
    if ( false === strpos( $matches[0], '>' ) ) {
        return esc_html( $matches[0] );
    }
    return $matches[0];
}

/**
 * Properly strip all HTML tags including script and style
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. wp_strip_all_tags will return ''
 *
 * @since 2.9.0
 *
 * @param string $string        String containing HTML tags
 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 * @return string The processed string.
 */
function lara_strip_all_tags( $string, $remove_breaks = false ) {
    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
    $string = strip_tags( $string );

    if ( $remove_breaks ) {
        $string = preg_replace( '/[\r\n\t ]+/', ' ', $string );
    }

    return trim( $string );
}

/**
 * Remove slashes from a string or array of strings.
 *
 * This should be used to remove slashes from data passed to core API that
 * expects data to be unslashed.
 *
 * @since 3.6.0
 *
 * @param string|array $value String or array of strings to unslash.
 * @return string|array Unslashed $value
 */
function lara_unslash( $value ) {
    return stripslashes_deep( $value );
}

/**
 * Navigates through an array, object, or scalar, and removes slashes from the values.
 *
 * @since 2.0.0
 *
 * @param mixed $value The value to be stripped.
 * @return mixed Stripped value.
 */
function stripslashes_deep( $value ) {
    return map_deep( $value, 'stripslashes_from_strings_only' );
}


/**
 * Callback function for `stripslashes_deep()` which strips slashes from strings.
 *
 * @since 4.4.0
 *
 * @param mixed $value The array or string to be stripped.
 * @return mixed $value The stripped value.
 */
function stripslashes_from_strings_only( $value ) {
    return is_string( $value ) ? stripslashes( $value ) : $value;
}

/**
 * Maps a function to all non-iterable elements of an array or an object.
 *
 * This is similar to `array_walk_recursive()` but acts upon objects too.
 *
 * @since 4.4.0
 *
 * @param mixed    $value    The array, object, or scalar.
 * @param callable $callback The function to map onto $value.
 * @return mixed The value with the callback applied to all non-arrays and non-objects inside it.
 */
function map_deep( $value, $callback ) {
    if ( is_array( $value ) ) {
        foreach ( $value as $index => $item ) {
            $value[ $index ] = map_deep( $item, $callback );
        }
    } elseif ( is_object( $value ) ) {
        $object_vars = get_object_vars( $value );
        foreach ( $object_vars as $property_name => $property_value ) {
            $value->$property_name = map_deep( $property_value, $callback );
        }
    } else {
        $value = call_user_func( $callback, $value );
    }

    return $value;
}

function get_page_title( $url, $page_title = null ) {
    if( $page_title ) {
        return $page_title;
    }else {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', $url);
        if( $res->getStatusCode() ) {
            $str = trim( preg_replace('/\s+/', ' ', $res->getBody()->getContents()) );
            preg_match( '/<title(.*?)>(.*)<\/title>/i',$str, $title );

            return htmlspecialchars_decode($title[2]);
        }
    }
}
