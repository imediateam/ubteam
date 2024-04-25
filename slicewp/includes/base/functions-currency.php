<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Returns an array with the currency codes and their names
 *
 * @param string $dataset
 *
 * @return array
 *
 */
function slicewp_get_currencies( $dataset = 'name' ) {

    $currencies = array(
        'USD' => array(
            'name'                => __( 'US Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'EUR' => array(
            'name'                => __( 'Euro', 'slicewp' ),
            'symbol'              => '&#8364;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'GBP' => array(
            'name'                => __( 'Pound Sterling', 'slicewp' ),
            'symbol'              => '&#163;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'CAD' => array(
            'name'                => __( 'Canadian Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'AUD' => array(
            'name'                => __( 'Australian Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'ARS' => array(
            'name'                => __( 'Argentine peso', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'BRL' => array(
            'name'                => __( 'Brazilian Real', 'slicewp' ),
            'symbol'              => '&#82;&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'BGN' => array(
            'name'                => __( 'Bulgarian lev', 'slicewp' ),
            'symbol'              => '&#1083;&#1074;.',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'HRK' => array(
            'name'                => __( 'Croatian Kuna', 'slicewp' ),
            'symbol'              => '&#107;&#110;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'CZK' => array(
            'name'                => __( 'Czech Koruna', 'slicewp' ),
            'symbol'              => '&#75;&#269;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'DKK' => array(
            'name'                => __( 'Danish Krone', 'slicewp' ),
            'symbol'              => '&#107;&#114;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'EGP' => array(
            'name'                => __( 'Egyptian Pound', 'slicewp' ),
            'symbol'              => '&#69;&#163;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'HKD' => array(
            'name'                => __( 'Hong Kong Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'HUF' => array(
            'name'                => __( 'Hungarian Forint', 'slicewp' ),
            'symbol'              => '&#70;&#116;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'ISK' => array(
            'name'                => __( 'Icelandic Króna', 'slicewp' ),
            'symbol'              => '&#107;&#114;',
            'symbol_position'     => 'after',
            'decimal_places'      => 0,
            'thousands_separator' => '.',
            'decimal_separator'   => ''
        ),
        'ILS' => array(
            'name'                => __( 'Israeli New Sheqel', 'slicewp' ),
            'symbol'              => '&#8362;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'INR' => array(
            'name'                => __( 'Indian Rupee', 'slicewp' ),
            'symbol'              => '&#8377;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'IDR' => array(
            'name'                => __( 'Indonesian rupiah', 'slicewp' ),
            'symbol'              => '&#82;&#112;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'JPY' => array(
            'name'                => __( 'Japanese Yen', 'slicewp' ),
            'symbol'              => '&#165;',
            'symbol_position'     => 'before',
            'decimal_places'      => 0,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'MYR' => array(
            'name'                => __( 'Malaysian Ringgit', 'slicewp' ),
            'symbol'              => '&#82;&#77;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'MXN' => array(
            'name'                => __( 'Mexican Peso', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'MDL' => array(
            'name'                => __( 'Moldovan Leu', 'slicewp' ),
            'symbol'              => 'MDL',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'NGN' => array(
            'name'                => __( 'Nigerian Naira', 'slicewp' ),
            'symbol'              => '&#8358;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'NZD' => array(
            'name'                => __( 'New Zealand Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'NOK' => array(
            'name'                => __( 'Norwegian Krone', 'slicewp' ),
            'symbol'              => '&#107;&#114;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'PHP' => array(
            'name'                => __( 'Philippine Peso', 'slicewp' ),
            'symbol'              => '&#8369;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'PLN' => array(
            'name'                => __( 'Polish Zloty', 'slicewp' ),
            'symbol'              => '&#122;&#322;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'RON' => array(
            'name'                => __( 'Romanian Leu', 'slicewp' ),
            'symbol'              => '&#76;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'RUB' => array(
            'name'                => __( 'Russian Ruble', 'slicewp' ),
            'symbol'              => '&#1088;&#1091;&#1073;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'RSD' => array(
            'name'                => __( 'Serbian dinar', 'slicewp' ),
            'symbol'              => '&#x434;&#x438;&#x43d;.',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'SGD' => array(
            'name'                => __( 'Singapore Dollar', 'slicewp' ),
            'symbol'              => '&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'ZAR' => array(
            'name'                => __( 'South African Rand', 'slicewp' ),
            'symbol'              => '&#82;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => '.',
        ),
        'KRW' => array(
            'name'                => __( 'South Korean Won', 'slicewp' ),
            'symbol'              => '&#8361;',
            'symbol_position'     => 'before',
            'decimal_places'      => 0,
            'thousands_separator' => ',',
            'decimal_separator'   => '.',
        ),
        'SEK' => array(
            'name'                => __( 'Swedish Krona', 'slicewp' ),
            'symbol'              => '&#107;&#114;',
            'symbol_position'     => 'after',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'CHF' => array(
            'name'                => __( 'Swiss Franc', 'slicewp' ),
            'symbol'              => '&#67;&#72;&#70;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => '.'
        ),
        'TWD' => array(
            'name'                => __( 'Taiwan New Dollar', 'slicewp' ),
            'symbol'              => '&#78;&#84;&#36;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'THB' => array(
            'name'                => __( 'Thai Baht', 'slicewp' ),
            'symbol'              => '&#3647;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ',',
            'decimal_separator'   => '.'
        ),
        'TRY' => array(
            'name'                => __( 'Turkish Lira', 'slicewp' ),
            'symbol'              => '&#8356;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'UAH' => array(
            'name'                => __( 'Ukrainian hryvnia', 'slicewp' ),
            'symbol'              => '&#8372;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => ' ',
            'decimal_separator'   => ','
        ),
        'UYU' => array(
            'name'                => __( 'Uruguayan Peso', 'slicewp' ),
            'symbol'              => '&#36;&#85;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
        'VEF' => array(
            'name'                => __( 'Venezuelan bolívar', 'slicewp' ),
            'symbol'              => '&#66;&#115;',
            'symbol_position'     => 'before',
            'decimal_places'      => 2,
            'thousands_separator' => '.',
            'decimal_separator'   => ','
        ),
    );

    // If a certain dataset is requested, remov
    if( $dataset != 'all' ) {

        $currencies = wp_list_pluck( $currencies, $dataset );

    }

    /**
     * Filter the currencies before returning
     *
     * @param array  $currencies
     * @param string $dataset
     *
     */
    $currencies = apply_filters( 'slicewp_currencies', $currencies, $dataset );

    return $currencies;

}


/**
 * Given a currency code returns a string with the currency symbol as HTML entity
 *
 * @param string $currency_code
 *
 * @return string
 *
 */
function slicewp_get_currency_symbol( $currency_code ) {

    $currency_symbols = slicewp_get_currencies( 'symbol' );

    /**
     * Filter the currency symbols before returning the needed symbol
     *
     * @param array $currency_symbols
     *
     */
    $currency_symbols = apply_filters( 'slicewp_currency_symbols', $currency_symbols );

    $currency_symbol = ( isset( $currency_symbols[$currency_code] ) ? $currency_symbols[$currency_code] : '' );

    return $currency_symbol;

}


/**
 * Formats the given amount and currency based on the saved settings
 *
 * @param string $amount
 * @param string $currency
 *
 * @return string
 *
 */
function slicewp_format_amount( $amount, $currency, $show_currency = true ) {

    // Get the currency decimal places
    $decimal_places = slicewp_get_currencies( 'decimal_places' );
    $decimal_places = ( isset( $decimal_places[$currency] ) ? absint( $decimal_places[$currency] ) : 2 );

    // Format number to two decimals
    $amount = number_format( (float)$amount, $decimal_places, slicewp_get_setting( 'currency_decimal_separator', '.' ), slicewp_get_setting( 'currency_thousands_separator', '' ) );

    // If show currency is true, add the currency symbol to the appropiate position
    if( $show_currency ) {

        // Get the currency position set in the settings page
        $currency_position = slicewp_get_setting( 'currency_symbol_position', 'before' );

        // Get the currency symbol for the currency
        $currency_symbol = slicewp_get_currency_symbol( $currency );
        $currency_symbol = ( ! empty( $currency_symbol ) ? $currency_symbol : $currency );

        // Set the format for the amount
        switch( $currency_position ) {

            case 'before':
                $format = '%1$s%2$s';
                break;

            case 'after':
                $format = '%2$s%1$s';
                break;

            case 'before_space':
                $format = '%1$s&nbsp;%2$s';
                break;

            case 'after_space':
                $format = '%2$s&nbsp;%1$s';
                break;

            default:
                $format = '%1$s%2$s';
                break;

        }

        /**
         * Filter the amount format
         *
         * @param string $format
         *
         */
        $format = apply_filters( 'slicewp_amount_format', $format );

        // Format the output
        $formatted_amount = sprintf( $format, $currency_symbol, $amount );

    // If show currency is false, just return the formatted amount, without the currency symbol
    } else {

        $formatted_amount = $amount;

    }

    return $formatted_amount;

}


/**
 * Sanitizes and formats the given amount to be database ready
 * The preferred scenario would be to have the values saved with two decimals, separated by . (dot)
 *
 * @param mixed $amount
 *
 * @return string
 *
 */
function slicewp_sanitize_amount( $amount ) {

    // Set any possible separators into an array
    $locale     = localeconv();
    $separators = array( '.', ',', ' ', $locale['decimal_point'], $locale['mon_decimal_point'] );

    // Replace any separators with dots
    $amount = str_replace( $separators, '.', $amount );

    // Remove all dots, but the last one (in case we have decimals)
    $amount = preg_replace( '/\.(?![^.]+$)|[^0-9.-]/', '', $amount );

    /**
     * Filter the number of decimals to be sed when sanitizing the amount
     *
     * @param int
     *
     */
    $decimals = apply_filters( 'slicewp_sanitize_amount_decimals', 2 );

    // Format the amount
    $amount = number_format( (float)$amount, $decimals, '.', '' );

    return $amount;

}


/**
 * Localizes currencies on the admin side
 *
 */
function slicewp_enqueue_admin_scripts_currencies() {

    wp_localize_script( 'slicewp-script', 'slicewp_currencies', slicewp_get_currencies( 'all' ) );
    
}
add_action( 'slicewp_enqueue_admin_scripts', 'slicewp_enqueue_admin_scripts_currencies' );