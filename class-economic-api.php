<?php
//php.ini overriding necessary for communicating with the SOAP server.
//ini_set('display_errors',1);
//ini_set('display_startup_errors',1);
//error_reporting(1);
if ( ! function_exists( 'logthis' ) ) {
    function logthis($msg) {
        if(TESTING){
			$filePath = dirname(__FILE__).'/logfile.log';
			$archivedFilePath = dirname(__FILE__).'/logfile_archived.log';
			if(file_exists($filePath) && ceil(filesize($filePath)/(1024*1024)) > 2){
				rename($filePath, $archivedFilePath);
			}
            if(!file_exists($filePath)){
                $fileobject = fopen($filePath, 'a');
                chmod($filePath, 0666);
            }
            else{
                $fileobject = fopen($filePath, 'a');
            }
            if(is_array($msg) || is_object($msg)){
                fwrite($fileobject,print_r($msg, true));
            }
            else{
                fwrite($fileobject,date("Y-m-d H:i:s"). ":" . $msg . "\n");
            }
        }
        else{
            error_log($msg);
        }
    }
}
ini_set("default_socket_timeout", 6000);
class WCE_API{

    /** @public String base URL */
    public $api_url;
	
	/** @public String license key */
    public $license_key;
    
    /** @public String Agreement Number */
    //public $agreementNumber;

    /** @public String User Name */
    //public $username;

    /** @public String Password */
    //public $password;

    /** @public String access ID or token */
    public $token;
	
	/** @public String private access ID or appToken */
    public $appToken;
	
	/** @public String local key data */
    public $localkeydata;
	
	/** @public Number corresponding the product group */
    public $product_group;
	
	/** @public Number corresponding the shipping group */
	public $shipping_group;
	
	/** @public Number corresponding the coupon group */
	public $coupon_group;	
	
	/** @public alphanumber corresponding the product offset */
    public $product_offset;
	
	/** @public Number corresponding the customer group */
    public $customer_group;
	
	/** @public alphanumber corresponding the customer offset */
    //public $customer_offset;
	
	/** @public string yes/no */
    //public $activate_allsync;
	
	/** @public string yes/no */
    public $activate_oldordersync;
	
	public $product_sync;
	
	public $product_stock_sync;
	
	/** @public alphanumber corresponding the order referernce offset */
    public $order_reference_prefix;
	
	
	/** @public array including all the customer meta fiedls that are snyned */
	public $user_fields = array(
	  'billing_phone',
	  'billing_email',
	  'billing_country',
	  'billing_address_1',
	  //'billing_address_2',
	  //'billing_state',
	  'billing_postcode',
	  'billing_city',
	  'billing_country',
	  'billing_company',
	  'billing_last_name',
	  'billing_first_name',
	  'billing_ean_number',
	  'billing_vat_number',
	  'billing_cin_number',
	
	  'shipping_phone',
	  'shipping_email',
	  'shipping_country',
	  'shipping_address_1',
	  //'shipping_address_2',
	  //'shipping_state',
	  'shipping_postcode',
	  'shipping_city',
	  'shipping_country',
	  'shipping_company',
	  'shipping_last_name',
	  'shipping_first_name'
	);
	
	public $eu = array(
		'BE' => 'Belgium',
		'BG' => 'Bulgaria',
		'CZ' => 'Czech Republic',
		'DK' => 'Denmark',
		'GE' => 'Germany',
		'EE' => 'Estonia',
		'IE' => 'Republic of Ireland',
		'EL' => 'Greece',
		'ES' => 'Spain',
		'FR' => 'France',
		'HR' => 'Croatia',
		'IT' => 'Italy',
		'CY' => 'Cyprus',
		'LV' => 'Latvia',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'HU' => 'Hungary',
		'MT' => 'Malta',
		'NL' => 'Netherlands',
		'AT' => 'Austria',
		'PL' => 'Poland',
		'PT' => 'Portugal',
		'RO' => 'Romania',
		'SI' => 'Slovenia',
		'SK' => 'Slovakia',
		'FI' => 'Finland',
		'SE' => 'Sweden',
		'GB' => 'United Kingdom'
	  );
	  
	public $countrycodes = array (
	  'AF' => 'Afghanistan',
	  'AX' => 'Åland Islands',
	  'AL' => 'Albania',
	  'DZ' => 'Algeria',
	  'AS' => 'American Samoa',
	  'AD' => 'Andorra',
	  'AO' => 'Angola',
	  'AI' => 'Anguilla',
	  'AQ' => 'Antarctica',
	  'AG' => 'Antigua and Barbuda',
	  'AR' => 'Argentina',
	  'AU' => 'Australia',
	  'AT' => 'Austria',
	  'AZ' => 'Azerbaijan',
	  'BS' => 'Bahamas',
	  'BH' => 'Bahrain',
	  'BD' => 'Bangladesh',
	  'BB' => 'Barbados',
	  'BY' => 'Belarus',
	  'BE' => 'Belgium',
	  'BZ' => 'Belize',
	  'BJ' => 'Benin',
	  'BM' => 'Bermuda',
	  'BT' => 'Bhutan',
	  'BO' => 'Bolivia',
	  'BA' => 'Bosnia and Herzegovina',
	  'BW' => 'Botswana',
	  'BV' => 'Bouvet Island',
	  'BR' => 'Brazil',
	  'IO' => 'British Indian Ocean Territory',
	  'BN' => 'Brunei Darussalam',
	  'BG' => 'Bulgaria',
	  'BF' => 'Burkina Faso',
	  'BI' => 'Burundi',
	  'KH' => 'Cambodia',
	  'CM' => 'Cameroon',
	  'CA' => 'Canada',
	  'CV' => 'Cape Verde',
	  'KY' => 'Cayman Islands',
	  'CF' => 'Central African Republic',
	  'TD' => 'Chad',
	  'CL' => 'Chile',
	  'CN' => 'China',
	  'CX' => 'Christmas Island',
	  'CC' => 'Cocos (Keeling) Islands',
	  'CO' => 'Colombia',
	  'KM' => 'Comoros',
	  'CG' => 'Congo',
	  'CD' => 'Zaire',
	  'CK' => 'Cook Islands',
	  'CR' => 'Costa Rica',
	  'CI' => 'Côte D\'Ivoire',
	  'HR' => 'Croatia',
	  'CU' => 'Cuba',
	  'CY' => 'Cyprus',
	  'CZ' => 'Czech Republic',
	  'DK' => 'Denmark',
	  'DJ' => 'Djibouti',
	  'DM' => 'Dominica',
	  'DO' => 'Dominican Republic',
	  'EC' => 'Ecuador',
	  'EG' => 'Egypt',
	  'SV' => 'El Salvador',
	  'GQ' => 'Equatorial Guinea',
	  'ER' => 'Eritrea',
	  'EE' => 'Estonia',
	  'ET' => 'Ethiopia',
	  'FK' => 'Falkland Islands (Malvinas)',
	  'FO' => 'Faroe Islands',
	  'FJ' => 'Fiji',
	  'FI' => 'Finland',
	  'FR' => 'France',
	  'GF' => 'French Guiana',
	  'PF' => 'French Polynesia',
	  'TF' => 'French Southern Territories',
	  'GA' => 'Gabon',
	  'GM' => 'Gambia',
	  'GE' => 'Georgia',
	  'DE' => 'Germany',
	  'GH' => 'Ghana',
	  'GI' => 'Gibraltar',
	  'GR' => 'Greece',
	  'GL' => 'Greenland',
	  'GD' => 'Grenada',
	  'GP' => 'Guadeloupe',
	  'GU' => 'Guam',
	  'GT' => 'Guatemala',
	  'GG' => 'Guernsey',
	  'GN' => 'Guinea',
	  'GW' => 'Guinea-Bissau',
	  'GY' => 'Guyana',
	  'HT' => 'Haiti',
	  'HM' => 'Heard Island and Mcdonald Islands',
	  'VA' => 'Vatican City State',
	  'HN' => 'Honduras',
	  'HK' => 'Hong Kong',
	  'HU' => 'Hungary',
	  'IS' => 'Iceland',
	  'IN' => 'India',
	  'ID' => 'Indonesia',
	  'IR' => 'Iran, Islamic Republic of',
	  'IQ' => 'Iraq',
	  'IE' => 'Ireland',
	  'IM' => 'Isle of Man',
	  'IL' => 'Israel',
	  'IT' => 'Italy',
	  'JM' => 'Jamaica',
	  'JP' => 'Japan',
	  'JE' => 'Jersey',
	  'JO' => 'Jordan',
	  'KZ' => 'Kazakhstan',
	  'KE' => 'KENYA',
	  'KI' => 'Kiribati',
	  'KP' => 'Korea, Democratic People\'s Republic of',
	  'KR' => 'Korea, Republic of',
	  'KW' => 'Kuwait',
	  'KG' => 'Kyrgyzstan',
	  'LA' => 'Lao People\'s Democratic Republic',
	  'LV' => 'Latvia',
	  'LB' => 'Lebanon',
	  'LS' => 'Lesotho',
	  'LR' => 'Liberia',
	  'LY' => 'Libyan Arab Jamahiriya',
	  'LI' => 'Liechtenstein',
	  'LT' => 'Lithuania',
	  'LU' => 'Luxembourg',
	  'MO' => 'Macao',
	  'MK' => 'Macedonia, the Former Yugoslav Republic of',
	  'MG' => 'Madagascar',
	  'MW' => 'Malawi',
	  'MY' => 'Malaysia',
	  'MV' => 'Maldives',
	  'ML' => 'Mali',
	  'MT' => 'Malta',
	  'MH' => 'Marshall Islands',
	  'MQ' => 'Martinique',
	  'MR' => 'Mauritania',
	  'MU' => 'Mauritius',
	  'YT' => 'Mayotte',
	  'MX' => 'Mexico',
	  'FM' => 'Micronesia, Federated States of',
	  'MD' => 'Moldova, Republic of',
	  'MC' => 'Monaco',
	  'MN' => 'Mongolia',
	  'ME' => 'Montenegro',
	  'MS' => 'Montserrat',
	  'MA' => 'Morocco',
	  'MZ' => 'Mozambique',
	  'MM' => 'Myanmar',
	  'NA' => 'Namibia',
	  'NR' => 'Nauru',
	  'NP' => 'Nepal',
	  'NL' => 'Netherlands',
	  'AN' => 'Netherlands Antilles',
	  'NC' => 'New Caledonia',
	  'NZ' => 'New Zealand',
	  'NI' => 'Nicaragua',
	  'NE' => 'Niger',
	  'NG' => 'Nigeria',
	  'NU' => 'Niue',
	  'NF' => 'Norfolk Island',
	  'MP' => 'Northern Mariana Islands',
	  'NO' => 'Norway',
	  'OM' => 'Oman',
	  'PK' => 'Pakistan',
	  'PW' => 'Palau',
	  'PS' => 'Palestinian Territory, Occupied',
	  'PA' => 'Panama',
	  'PG' => 'Papua New Guinea',
	  'PY' => 'Paraguay',
	  'PE' => 'Peru',
	  'PH' => 'Philippines',
	  'PN' => 'Pitcairn',
	  'PL' => 'Poland',
	  'PT' => 'Portugal',
	  'PR' => 'Puerto Rico',
	  'QA' => 'Qatar',
	  'RE' => 'Réunion',
	  'RO' => 'Romania',
	  'RU' => 'Russian Federation',
	  'RW' => 'Rwanda',
	  'SH' => 'Saint Helena',
	  'KN' => 'Saint Kitts and Nevis',
	  'LC' => 'Saint Lucia',
	  'PM' => 'Saint Pierre and Miquelon',
	  'VC' => 'Saint Vincent and the Grenadines',
	  'WS' => 'Samoa',
	  'SM' => 'San Marino',
	  'ST' => 'Sao Tome and Principe',
	  'SA' => 'Saudi Arabia',
	  'SN' => 'Senegal',
	  'RS' => 'Serbia',
	  'SC' => 'Seychelles',
	  'SL' => 'Sierra Leone',
	  'SG' => 'Singapore',
	  'SK' => 'Slovakia',
	  'SI' => 'Slovenia',
	  'SB' => 'Solomon Islands',
	  'SO' => 'Somalia',
	  'ZA' => 'South Africa',
	  'GS' => 'South Georgia and the South Sandwich Islands',
	  'ES' => 'Spain',
	  'LK' => 'Sri Lanka',
	  'SD' => 'Sudan',
	  'SR' => 'Suriname',
	  'SJ' => 'Svalbard and Jan Mayen',
	  'SZ' => 'Swaziland',
	  'SE' => 'Sweden',
	  'CH' => 'Switzerland',
	  'SY' => 'Syrian Arab Republic',
	  'TW' => 'Taiwan, Province of China',
	  'TJ' => 'Tajikistan',
	  'TZ' => 'Tanzania, United Republic of',
	  'TH' => 'Thailand',
	  'TL' => 'Timor-Leste',
	  'TG' => 'Togo',
	  'TK' => 'Tokelau',
	  'TO' => 'Tonga',
	  'TT' => 'Trinidad and Tobago',
	  'TN' => 'Tunisia',
	  'TR' => 'Turkey',
	  'TM' => 'Turkmenistan',
	  'TC' => 'Turks and Caicos Islands',
	  'TV' => 'Tuvalu',
	  'UG' => 'Uganda',
	  'UA' => 'Ukraine',
	  'AE' => 'United Arab Emirates',
	  'GB' => 'United Kingdom',
	  'US' => 'United States',
	  'UM' => 'United States Minor Outlying Islands',
	  'UY' => 'Uruguay',
	  'UZ' => 'Uzbekistan',
	  'VU' => 'Vanuatu',
	  'VE' => 'Venezuela',
	  'VN' => 'Viet Nam',
	  'VG' => 'Virgin Islands, British',
	  'VI' => 'Virgin Islands, U.S.',
	  'WF' => 'Wallis and Futuna',
	  'EH' => 'Western Sahara',
	  'YE' => 'Yemen',
	  'ZM' => 'Zambia',
	  'ZW' => 'Zimbabwe',
	);
	
	public $product_lock;
	
	//public $shipping_product_id;

    /**
     *
     */
    function __construct() {

        $options = get_option('woocommerce_economic_general_settings');
		
        $this->localkeydata = get_option('local_key_economic_plugin');
        $this->api_url = dirname(__FILE__)."/EconomicWebservice.asmx.xml";
		//$this->api_url = 'https://api.e-conomic.com/secure/api1/EconomicWebservice.asmx?WSDL';
        $this->license_key = isset($options['license-key'])? $options['license-key'] : '';
		
		$this->token = isset($options['token'])? $options['token'] : '';
		$this->appToken = '15MjebGLGLPv4_I90Wy8EqzcXwThPmrY5iRNlG0H3_w1';
		$this->product_sync = isset($options['product-sync'])? $options['product-sync'] : '';
		$this->product_stock_sync = isset($options['product-stock-sync'])? $options['product-stock-sync'] : '';
		$this->other_checkout = isset($options['other-checkout'])? $options['other-checkout'] : '';
		$this->economic_checkout = isset($options['economic-checkout'])? $options['economic-checkout'] : '';
		$this->activate_oldordersync = isset($options['activate-oldordersync'])? $options['activate-oldordersync'] : '';
		$this->product_group = isset($options['product-group'])? $options['product-group']: '';
		$this->product_offset = isset($options['product-prefix'])? $options['product-prefix']: '';
		$this->customer_group = isset($options['customer-group'])? $options['customer-group']: '';
		$this->shipping_group = isset($options['shipping-group'])? $options['shipping-group']: '';
		$this->coupon_group = isset($options['coupon-group'])? $options['coupon-group']: '';
		$this->order_reference_prefix = isset($options['order-reference-prefix'])? $options['order-reference-prefix'] : '';
		
		$this->product_lock = false;
    }

    /**
     * Create Connection to e-conomic
     *
     * @access public
     * @return object
     */
    public function woo_economic_client(){
	
	  //Added for 1.9.10 AppIdentifier change
	  $opts = array(
		  'http' => array(
			'header' => "X-EconomicAppIdentifier:  WooCommerce e-conomic Integration/1.9.25 (http://wooconomics.com/; support@wooconomics.com) PHP-SOAP/1.0\r\n"
		  )
	  );
	  $context = stream_context_create($opts);
	  
	  $client = new SoapClient($this->api_url, array("trace" => 1, "exceptions" => 1, 'stream_context' => $context));
	  
	  //Added for 1.9.10 AppIdentifier change
	
	  //logthis("woo_economic_client loaded token: " . $this->token . " appToken: " . $this->appToken);
	  if (!$this->token || !$this->appToken){
		logthis("e-conomic Access Token not defined!");
		return false;
	  }
		
	  //logthis("woo_economic_client - options are OK!");
	  //logthis("woo_economic_client - creating client...");
	  	  
	  try{
		 $client->ConnectWithToken(array(
			'token' 	=> $this->token,
			'appToken'  => $this->appToken));
	  }
	  catch (Exception $exception){
		logthis("Connection to client failed: " . $exception->getMessage());
		$this->debug_client($client);
		return false;
	  }
	  
	  logthis("woo_economic_client - client created");
	  return $client;
	}
	
	/**
     * Log the client connection request headers for debugging
     *
     * @access public
     * @return void
     */
	public function debug_client($client){
	  if (is_null($client)) {
		logthis("Client is null");
	  } else {
		logthis("-----LastRequestHeaders-------");
		logthis($client->__getLastRequestHeaders());
		logthis("------LastRequest------");
		logthis($client->__getLastRequest());
		logthis("------LastResponse------");
		logthis($client->__getLastResponse());
		logthis("------Debugging ends------");
	  }
	}

    /**
     * Creates a e-conomic HttpRequest
     *
     * @access public
     * @return bool
     */
    public function create_API_validation_request(){
		//logthis(get_option('woocommerce_economic_general_settings'));
        logthis("API VALIDATION");
        if(!isset($this->license_key)){
			logthis("API VALIDATION FAILED: license key not set!");
            return false;
        }
		
		if($this->woo_economic_client()){
			return true;
		}
		else{
			logthis("API VALIDATION FAILED: client not connected!");
			return false;
		}
    }

    /**
     * Creates a HttpRequest and appends the given XML to the request and sends it For license key
     *
     * @access public
     * @return bool
     */
    public function create_license_validation_request($localkey=''){
        logthis("LICENSE VALIDATION");
        if(!isset($this->license_key)){
            return false;
        }
        $licensekey = $this->license_key;
        // -----------------------------------
        //  -- Configuration Values --
        // -----------------------------------
        // Enter the url to your WHMCS installation here
        //$whmcsurl = 'http://176.10.250.47/whmcs/'; $whmcsurlsock = '176.10.250.47/whmcs';
        $whmcsurl = 'http://whmcs.onlineforce.net/'; $whmcsurlsock = 'whmcs.onlineforce.net';
        // Must match what is specified in the MD5 Hash Verification field
        // of the licensing product that will be used with this check.
        //$licensing_secret_key = 'itservice';
        $licensing_secret_key = 'ak4762';
        // The number of days to wait between performing remote license checks
        $localkeydays = 15;
        // The number of days to allow failover for after local key expiry
        $allowcheckfaildays = 5;

        // -----------------------------------
        //  -- Do not edit below this line --
        // -----------------------------------

        $check_token = time() . md5(mt_rand(1000000000, 9999999999) . $licensekey);
        $checkdate = date("Ymd");
        $domain = $_SERVER['SERVER_NAME'];
		$host= gethostname();
		//$usersip = gethostbyname($host);
        $usersip = gethostbyname($host) ? gethostbyname($host) : $_SERVER['SERVER_ADDR'];
        //$usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
        $dirpath = dirname(__FILE__);
        $verifyfilepath = 'modules/servers/licensing/verify.php';
        $localkeyvalid = false;
        if ($localkey) {
            $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
            $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
            $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
            if ($md5hash == md5($localdata . $licensing_secret_key)) {
                $localdata = strrev($localdata); # Reverse the string
                $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
                $localdata = substr($localdata, 32); # Extract License Data
                $localdata = base64_decode($localdata);
                $localkeyresults = unserialize($localdata);
                $originalcheckdate = $localkeyresults['checkdate'];
                if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                    $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                    if ($originalcheckdate > $localexpiry) {
                        $localkeyvalid = true;
                        $results = $localkeyresults;
                        $validdomains = explode(',', $results['validdomain']);
                        if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                        $validips = explode(',', $results['validip']);
                        if (!in_array($usersip, $validips)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                        $validdirs = explode(',', $results['validdirectory']);
                        if (!in_array($dirpath, $validdirs)) {
                            $localkeyvalid = false;
                            $localkeyresults['status'] = "Invalid";
                            $results = array();
                        }
                    }
                }
            }
        }
        if (!$localkeyvalid) {
            $postfields = array(
                'licensekey' => $licensekey,
                'domain' => $domain,
                'ip' => $usersip,
                'dir' => $dirpath,
            );
            if ($check_token) $postfields['check_token'] = $check_token;
            $query_string = '';
            foreach ($postfields AS $k=>$v) {
                $query_string .= $k.'='.urlencode($v).'&';
            }
            if (function_exists('curl_exec')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
            } else {
                $fp = fsockopen($whmcsurlsock, 80, $errno, $errstr, 5);
				//logthis($errstr.':'.$errno);
                if ($fp) {
                    $newlinefeed = "\r\n";
                    $header = "POST ".$whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
                    $header .= "Host: ".$whmcsurl . $newlinefeed;
                    $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
                    $header .= "Content-length: ".@strlen($query_string) . $newlinefeed;
                    $header .= "Connection: close" . $newlinefeed . $newlinefeed;
                    $header .= $query_string;
                    $data = '';
                    @stream_set_timeout($fp, 20);
                    @fputs($fp, $header);
                    $status = @socket_get_status($fp);
                    while (!@feof($fp)&&$status) {
                        $data .= @fgets($fp, 1024);
                        $status = @socket_get_status($fp);
                    }
                    @fclose ($fp);
                }
            }
            if (!$data) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
                if ($originalcheckdate > $localexpiry) {
                    $results = $localkeyresults;
                } else {
                    $results = array();
                    $results['status'] = "Invalid";
                    $results['description'] = "Remote Check Failed";
                    return $results;
                }
            } else {
                preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
                $results = array();
                foreach ($matches[1] AS $k=>$v) {
                    $results[$v] = $matches[2][$k];
                }
            }
            if (!is_array($results)) {
                die("Invalid License Server Response");
            }
            if (isset($results['md5hash'])) {
                if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
                    $results['status'] = "Invalid";
                    $results['description'] = "MD5 Checksum Verification Failed";
                    return $results;
                }
            }
            if ($results['status'] == "Active") {
                $results['checkdate'] = $checkdate;
                $data_encoded = serialize($results);
                $data_encoded = base64_encode($data_encoded);
                $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
                $data_encoded = strrev($data_encoded);
                $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
                $data_encoded = wordwrap($data_encoded, 80, "\n", true);
                $results['localkey'] = $data_encoded;
            }
            $results['remotecheck'] = true;
        }
        unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
        return $results;
        //return true;
    }
	
	
	/**
     * Get product SKU, concatenate product offset if not synced from ecnomic.
     *
     * @access public
     * @param product oject
     * @return product SKU string.
     */
	
	
	public function woo_get_product_sku(WC_Product $product){ //todo fix this function.
	  $synced_from_economic = get_post_meta($product->id, 'synced_from_economic', true);
	  $product_sku = null;
	  if (isset($synced_from_economic) && $synced_from_economic) {
		$product_sku = $product->sku;
	  } else {
		$product_sku = $this->product_offset.$product->sku;
	  }
	  return $product_sku;
	}
	
	/**
     * Get product SKU from economic
     *
     * @access public
     * @param product oject
     * @return product SKU string.
     */
		
	public function woo_get_product_sku_from_economic($product_id){
		$product_offset = $this->product_offset;
		if (strpos($product_id, $this->product_offset) === false) // this is an woocommerce product
			return $this->product_offset.$product->sku;
		else
			return $product_id;
	}
	
	
	/**
     * Save WooCommerce Order to e-conomic
     *
     * @access public
     * @param  Soap client object, user object or NULL, order object or NULL and refund flag.
     * @return bool
     */
	public function save_invoice_to_economic(SoapClient &$client, WP_User $user = NULL, WC_Order $order = NULL, $refund = NULL){
		global $wpdb;
		$draft_invoice_synced = false;
		try{
			
			$is_synced = $this->woo_is_order_synced_already($client, $order);
			
			if ($is_synced['synced'] === true && $is_synced['type'] === 'order' ) {
				$order_handle = $this->save_order_to_economic($client, $user, $order, $refund);
				
				if($order_handle !== true || $order_handle !== false){
					$current_invoice_handle = $client->Order_UpgradeToInvoice(array(
						'orderHandle' => $order_handle
					))->Order_UpgradeToInvoiceResult;
				}
				$draft_invoice_synced = true;
			}
			
			if ($is_synced['synced'] === true && $is_synced['type'] === 'invoice' ) {
				logthis("save_invoice_to_economic: Current invoice already sent as Invoice.");
				logthis($is_synced);
				$draft_invoice_synced = true;
			}
			
			if (($is_synced['synced'] === true && $is_synced['type'] === 'current_invoice') || $is_synced['synced'] === false ) {
				logthis("save_invoice_to_economic Getting debtor handle");
				$debtor_handle = $this->woo_get_debtor_handle_from_economic($client, $user, $order);
				//Added for add-on plugin.
				$modified_data = apply_filters( 'wooconomics_order_for_addon', array('client' => $client, 'order' => $order));
				if(is_object($modified_data['order'])){
					$order = $modified_data['order'];
				}
				if(is_object($modified_data['debtor_handle'])){
					$debtor_handle = $modified_data['debtor_handle'];
				}
				//Added for add-on plugin.
				if (!($debtor_handle)) {
					logthis("save_invoice_to_economic debtor not found, can not create invoice");
					return false;
				}
				
				if($is_synced['type'] === 'current_invoice'){
					$current_invoice_handle = $is_synced['handle'];
				}else{
					if(isset($order->order_number) && $order->order_number != NULL){
						$orderId = $order->order_number;
					}else{
						$orderId = $order->id;
					}
					$current_invoice_handle = $this->woo_get_current_invoice_from_economic($client, $this->order_reference_prefix.$orderId, $debtor_handle);
                    logthis("Adding order comments to Text line 2:".$order->customer_message);
                    $client->CurrentInvoice_SetTextLine2(array(
        				'currentInvoiceHandle' => $current_invoice_handle,
        				'value' => $order->customer_message
        			 ));
				}
				
				logthis("save_invoice_to_economic woo_get_current_invoice_from_economic returned current invoice handle.");
				logthis($current_invoice_handle);
				
				$countries = new WC_Countries();			
				
				$formatted_state = $countries->states[$order->billing_country][$order->billing_state];
				$address = trim($order->billing_address_1 . "\n" . $order->billing_address_2 . "\n" . $formatted_state);
				$city = $order->billing_city;
				$postalcode = $order->billing_postcode;
				//$country = $countries->countries[$order->billing_country];
				$country = $order->billing_country;
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtor.");
				$debtorName = $order->billing_company != ''? $order->billing_company : $order->billing_first_name.' '.$order->billing_last_name;
				$client->CurrentInvoice_SetDebtor(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'valueHandle' => $debtor_handle
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtorName.");
				$client->CurrentInvoice_SetDebtorName(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $debtorName
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtorAddress.");
				$client->CurrentInvoice_SetDebtorAddress(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $address
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtorCity.");
				$client->CurrentInvoice_SetDebtorCity(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $city
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtorCountry.");
				$client->CurrentInvoice_SetDebtorCountry(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $country
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDebtorPostalCode.");
				$client->CurrentInvoice_SetDebtorPostalCode(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $postalcode
				));
				
				
				$formatted_state = $countries->states[$order->shipping_country][$order->shipping_state];
				$address = trim($order->shipping_address_1 . "\n" . $order->shipping_address_2 . "\n" . $formatted_state);
				$city = $order->shipping_city;
				$postalcode = $order->shipping_postcode;
				//$country = $countries->countries[$order->shipping_country];
				$country = $order->shipping_country;
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDeliveryAddress.");
				$client->CurrentInvoice_SetDeliveryAddress(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $address
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDeliveryCity.");
				$client->CurrentInvoice_SetDeliveryCity(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $city
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDeliveryPostalCode.");
				$client->CurrentInvoice_SetDeliveryPostalCode(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $postalcode
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetDeliveryCountry.");
				$client->CurrentInvoice_SetDeliveryCountry(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $country
				));
				
				logthis("save_invoice_to_economic CurrentInvoice_SetCurrency.");
				
				
				//Added for version 1.9.17 to support WooCommerce Product Price Based on Countries
				if(isset($order->order_currency) && $order->order_currency != ''){
					$client->CurrentInvoice_SetCurrency(array(
						'currentInvoiceHandle' => $current_invoice_handle,
						'valueHandle' => array('Code' => $order->order_currency)
					));
				}else{
					$client->CurrentInvoice_SetCurrency(array(
						'currentInvoiceHandle' => $current_invoice_handle,
						'valueHandle' => array('Code' => get_option('woocommerce_currency'))
					));
				}
				
				//Added for version 1.9.9.8
				//Set the order date
				$date = new DateTime($order->order_date);
				$client->CurrentInvoice_SetDate(array(
					'currentInvoiceHandle' => $current_invoice_handle,
					'value' => $date->format('c')
				));
				
				$currentInvoiceLines = $client->CurrentInvoice_GetLines(array(
					'currentInvoiceHandle' => $current_invoice_handle,
				))->CurrentInvoice_GetLinesResult;
				
				if(isset($currentInvoiceLines->CurrentInvoiceLineHandle)){
					if(is_array($currentInvoiceLines->CurrentInvoiceLineHandle)){
						foreach($currentInvoiceLines->CurrentInvoiceLineHandle as $currentInvoiceLine){
							$client->CurrentInvoiceLine_Delete(array(
								'currentInvoiceLineHandle' => $currentInvoiceLine,
							));
						}
					}else{
						$client->CurrentInvoiceLine_Delete(array(
							'currentInvoiceLineHandle' => $currentInvoiceLines->CurrentInvoiceLineHandle,
						));
					}
				}
				
				logthis("save_invoice_to_economic call woo_handle_invoice_lines_to_economic.");			
				$this->woo_handle_invoice_lines_to_economic($order, $current_invoice_handle, $client, $refund);
				$draft_invoice_synced = true;
			}
			
			if($draft_invoice_synced === true){
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id.";")){
					$wpdb->update ($wpdb->prefix."wce_orders", array('synced' => 1), array('order_id' => $order->id), array('%d'), array('%d'));
				}else{
					$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 1), array('%d', '%d'));
				}
				return true;
			}else{
				if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id.";")){
					$wpdb->update ($wpdb->prefix."wce_orders", array('synced' => 0), array('order_id' => $order->id), array('%d'), array('%d'));
				}else{
					$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 0), array('%d', '%d'));
				}
				return false;
			}
		}catch (Exception $exception) {
			logthis("save_invoice_to_economic could not save order: " . $exception->getMessage());
			$this->debug_client($client);
			logthis('Could not create invoice.');
			logthis($exception->getMessage());
			if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id." AND synced=0;")){
				return false;
			}else{
				$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 0), array('%d', '%d'));
				return false;
			}
		}
	}
	
	/**
     * Get current invoice from economic
     *
     * @access public
     * @param User object, SOAP client
     * @return current invoice handle object
     */	
	public function woo_get_current_invoice_from_economic(SoapClient &$client, $reference, &$debtor_handle){
		$current_invoice_handle = $client->CurrentInvoice_FindByOtherReference(array(
			'otherReference' => $reference
		))->CurrentInvoice_FindByOtherReferenceResult;
		
		$current_invoice_handle = $current_invoice_handle->CurrentInvoiceHandle;
		
		$current_invoice_handle_array = (array) $current_invoice_handle;
		
		if (empty($current_invoice_handle_array)) {
			logthis("woo_get_current_invoice_from_economic create CurrentInvoiceHandle.");
			$current_invoice_handle = $client->CurrentInvoice_Create(array(
				'debtorHandle' => $debtor_handle
			))->CurrentInvoice_CreateResult;
			
			$client->CurrentInvoice_SetOtherReference(array(
				'currentInvoiceHandle' => $current_invoice_handle,
				'value' => $reference
			));
		}
		//logthis("current_invoice_handle: ".$current_invoice_handle);
		logthis("woo_get_current_invoice_from_economic invoice handle found and ID is: ");
		logthis($current_invoice_handle);
		return $current_invoice_handle;
	}
	
	
	/**
     * Get order lines handle
     *
     * @access public
     * @param Order object, Invoice handle object, SOAP client, refund bool
     * @return debtor_handle object
     */	
	public function woo_handle_invoice_lines_to_economic(WC_Order $order, $current_invoice_handle, SoapClient &$client, $refund){
	  logthis("woo_handle_invoice_lines_to_economic - get all lines");
	
	  foreach ($order->get_items() as $item) {
		$product = $order->get_product_from_item($item);
		if(isset($product) && !empty($product)){
			$current_invoice_line_handle = null;
			$current_invoice_line_handle = $this->woo_create_currentinvoice_orderline_at_economic($current_invoice_handle, $this->woo_get_product_sku($product), $client);
		
			logthis("woo_handle_invoice_lines_to_economic updating qty on id: " . $current_invoice_line_handle->Id . " number: " . $current_invoice_line_handle->Number);
			$quantity = ($refund) ? $item['qty'] * -1 : $item['qty'];
			$client->CurrentInvoiceLine_SetQuantity(array(
			  'currentInvoiceLineHandle' => $current_invoice_line_handle,
			  'value' => $quantity
			));
			
			$client->CurrentInvoiceLine_SetUnitNetPrice(array(
			  'currentInvoiceLineHandle' => $current_invoice_line_handle,
			  'value' => $item['line_subtotal']/$item['qty']
			));
			
			$discount = $item['line_subtotal']-$item['line_total'];
			if($discount != 0){
				$discount = ($discount*100)/$item['line_subtotal'];
				$client->CurrentInvoiceLine_SetDiscountAsPercent(array(
				  'currentInvoiceLineHandle' => $current_invoice_line_handle,
				  'value' => $discount
				));
			}

			//Added for add-on plugin.
			apply_filters( 'wooconomics_set_orderline_inventory_location', array('client' => $client, 'currentInvoiceLineHandle' => $current_invoice_line_handle, 'type' => 'currentInvoiceLineHandle'));
			//Added for add-on plugin.
			
			logthis("woo_handle_invoice_lines_to_economic updated line");
		}
	  }
	  
	    $shippingItem = reset($order->get_items('shipping'));
		//logthis($shippingItem['method_id']);
		if(isset($shippingItem['method_id'])){
			logthis("woo_handle_invoice_lines_to_economic adding Shipping line");
			if(strlen($shippingItem['method_id']) > 25){
				$shippingID = substr($shippingItem['method_id'], 0, 24);
			}else{
				$shippingID = $shippingItem['method_id'];
			}
			$current_invoice_line_handle = null;
			$current_invoice_line_handle = $this->woo_create_currentinvoice_orderline_at_economic($current_invoice_handle, $shippingID, $client);
			logthis("woo_handle_invoice_lines_to_economic updating qty on id: " . $current_invoice_line_handle->Id . " number: " . $current_invoice_line_handle->Number);
			$quantity = ($refund) ? $item['qty'] * -1 : 1;
			$client->CurrentInvoiceLine_SetQuantity(array(
				'currentInvoiceLineHandle' => $current_invoice_line_handle,
				'value' => $quantity
			));
			$client->CurrentInvoiceLine_SetUnitNetPrice(array(
				'currentInvoiceLineHandle' => $current_invoice_line_handle,
				'value' => $shippingItem['cost']
			));
			logthis("woo_handle_invoice_lines_to_economic updated shipping line");
		}
		
		//logthis('coupon');
		//logthis($coupon);
		$coupon = reset($order->get_items('coupon'));
		if(isset($coupon['name'])){
			logthis("woo_handle_invoice_lines_to_economic adding Coupon line");
			if(strlen($coupon['name']) > 25){
				$couponID = substr($coupon['name'], 0, 24);
			}else{
				$couponID = $coupon['name'];
			}
			$current_invoice_line_handle = null;
			$current_invoice_line_handle = $this->woo_create_currentinvoice_orderline_at_economic($current_invoice_handle, $couponID , $client);
			logthis("woo_handle_invoice_lines_to_economic updating qty on id: " . $current_invoice_line_handle->Id . " number: " . $current_invoice_line_handle->Number);
			$quantity = ($refund) ? -1 : 1;
			$client->CurrentInvoiceLine_SetQuantity(array(
				'currentInvoiceLineHandle' => $current_invoice_line_handle,
				'value' => $quantity
			));
			/*$client->CurrentInvoiceLine_SetUnitNetPrice(array(
				'currentInvoiceLineHandle' => $current_invoice_line_handle,
				'value' => -$coupon['discount_amount']
			));*/
			logthis("woo_handle_invoice_lines_to_economic updated coupon line");
		}
	}
	
	
	/**
     * Get invoice lines to e-conomic 
     *
     * @access public
     * @param 
     * @return current invoice line created for the order line.
     */
	public function woo_create_currentinvoice_orderline_at_economic($current_invoice_handle, $product_id, SoapClient &$client){
		$current_invoice_line_handle = $client->CurrentInvoiceLine_Create(array(
			'invoiceHandle' => $current_invoice_handle
		))->CurrentInvoiceLine_CreateResult;
		logthis("woo_create_currentinvoice_orderline_at_economic added line id: " . $current_invoice_line_handle->Id . " number: " . $current_invoice_line_handle->Number . " product_id: " . $product_id);
		$product_handle = $client->Product_FindByNumber(array(
			'number' => $product_id
		))->Product_FindByNumberResult;
		$client->CurrentInvoiceLine_SetProduct(array(
			'currentInvoiceLineHandle' => $current_invoice_line_handle,
			'valueHandle' => $product_handle
		));
		$product = $client->Product_GetData(array(
			'entityHandle' => $product_handle
		))->Product_GetDataResult;
		$client->CurrentInvoiceLine_SetDescription(array(
			'currentInvoiceLineHandle' => $current_invoice_line_handle,
			'value' => $product->Name
		));
		$client->CurrentInvoiceLine_SetUnitNetPrice(array(
			'currentInvoiceLineHandle' => $current_invoice_line_handle,
			'value' => $product->SalesPrice
		));
		
		logthis("woo_create_currentinvoice_orderline_at_economic added product to line ");
		return $current_invoice_line_handle;
	}

	
	/**
     * Get debtor handle from economic
     *
     * @access public
     * @param User object, SOAP client
     * @return debtor_handle object
     */
	public function woo_get_debtor_handle_from_economic(SoapClient &$client, WP_User $user = NULL, WC_Order $order = NULL){
		try {
			if(is_object($user)){
				$debtorNumber = $user->get('debtor_number');
				$debtor_handle = NULL;
				logthis("woo_get_debtor_handle_from_economic trying to load : " . $debtorNumber);
				if (!isset($debtorNumber) || empty($debtorNumber)) {
				    logthis("woo_get_debtor_handle_from_economic: Getting debtor handle using email!");
					$debtor_handles = $client->Debtor_FindByEmail(array(
						'email' => get_user_meta($user->ID, 'billing_email', true)
					))->Debtor_FindByEmailResult;
                    $debtor_handle = $debtor_handles->DebtorHandle;
				}else{
					$debtor_handle = $client->Debtor_FindByNumber(array(
						'number' => $debtorNumber
					))->Debtor_FindByNumberResult;
				}
			}else{
				if(is_object($order) && $order->billing_email != ''){
					logthis("woo_get_debtor_handle_from_economic user not defined, guest user suspected, fetching debtorNumber by order email: ".$order->billing_email);
					$debtor_handles = $client->Debtor_FindByEmail(array(
						'email' => $order->billing_email
					))->Debtor_FindByEmailResult;
					$debtor_handle = $debtor_handles->DebtorHandle;
					//logthis($debtor_handle);
				}
				else{
					logthis("woo_get_debtor_handle_from_economic user not defined, guest user suspected, fetching debtorNumber by email: ".$user);
					$debtor_handles = $client->Debtor_FindByEmail(array(
						'email' => $user
					))->Debtor_FindByEmailResult;
					$debtor_handle = $debtor_handles->DebtorHandle;
				}
			}	
            
			
			$debtor_handle_array = (array) $debtor_handle;	
			
			$tax_based_on = get_option('woocommerce_tax_based_on');
			$vatZone = 'HomeCountry';
			
			if($tax_based_on == 'billing'){
				$vatZone = $this->woo_get_debtor_vat_zone('billing', $user, $order);
			}elseif($tax_based_on == 'shipping'){
				$vatZone = $this->woo_get_debtor_vat_zone('shipping', $user, $order);
			}else{
				$vatZone = 'HomeCountry';
			}
			
			if (!empty($debtor_handle_array)) {
				logthis("woo_get_debtor_handle_from_economic debtor found for user.");
				//logthis($user != NULL? $user->ID : $order->billing_email);
				logthis($debtor_handle);
				if(is_array($debtor_handle)){
					$debtor_handle = $debtor_handle[0];
				}
                update_user_meta($user->ID, 'debtor_number', $debtor_handle->Number);
				$client->Debtor_SetVatZone(array(
						//'number' => $user->ID,
						'debtorHandle' => $debtor_handle,
						'value' => $vatZone
					)
				);
			}
			else {
				// The debtor doesn't exist - lets create it
				logthis("woo_get_debtor_handle_from_economic debtor doesn't exit, creating debtor");
				$debtor_grouphandle_meta = $this->customer_group;
				logthis("woo_get_debtor_handle_from_economic debtor group: " . $debtor_grouphandle_meta);
				//logthis($user);
				
				if($user != NULL){	
					$billing_first_name = $user->get('billing_first_name');
					if(isset($billing_first_name) && $billing_first_name != ''){
						$debtor_name = $user->get('billing_first_name').' '.$user->get('billing_last_name');
					}else{
						$debtor_name = $user->get('billing_company');
					}
					
					logthis("woo_get_debtor_handle_from_economic name: " . $debtor_name);
					//logthis("woo_get_debtor_handle_from_economic billing_comnpany: " . $billing_company);
				
					$debtor_grouphandle = $client->DebtorGroup_FindByNumber(array(
						'number' => $debtor_grouphandle_meta
					))->DebtorGroup_FindByNumberResult;					
					
					$debtor_handle = $client->Debtor_Create(array(
						//'number' => $user->ID,
						'debtorGroupHandle' => $debtor_grouphandle,
						'name' => $debtor_name,
						'vatZone' => $vatZone
					))->Debtor_CreateResult;
					
					update_user_meta($user->ID, 'debtor_number', $debtor_handle->Number);
					logthis("woo_get_debtor_handle_from_economic debtor created using user object: " . $name);
				}else{
					logthis("woo_get_debtor_handle_from_economic name: " . $order->billing_first_name. " " . $order->billing_last_name);
					logthis("woo_get_debtor_handle_from_economic billing_comnpany: " . $order->billing_company);
				
					$debtor_grouphandle = $client->DebtorGroup_FindByNumber(array(
						'number' => $debtor_grouphandle_meta
					))->DebtorGroup_FindByNumberResult;
				
					$debtor_number = mt_rand( 9999, 99999 );
					
					if(isset($order->billing_company) && $order->billing_company != ''){
						$debtor_name = $order->billing_company;
					}else{
						$debtor_name = $order->billing_first_name.' '.$order->billing_last_name;
					}
				
					$debtor_handle = $client->Debtor_Create(array(
						//'number' => $debtor_number,
						'debtorGroupHandle' => $debtor_grouphandle,
						'name' => $debtor_name,
						'vatZone' => $vatZone
					))->Debtor_CreateResult;
					
					update_user_meta($user->ID, 'debtor_number', $debtor_handle->Number);
					logthis("woo_get_debtor_handle_from_economic debtor created using order object: " . $order->billing_email);
				}
				//logthis("woo_get_debtor_handle_from_economic debtor created for user->id " . $user != NULL? $user->ID : $order->billing_email);
			}
			
			if(is_array($debtor_handle)){
				$debtor_handle = $debtor_handle[0];
			}	
            
            if($order != NULL && $order){
                $client->Debtor_SetCurrency(array(
    				'debtorHandle' => $debtor_handle,
    				'valueHandle' => array('Code' => $order->order_currency)
    			));	
            }
            else{
               $client->Debtor_SetCurrency(array(
    				'debtorHandle' => $debtor_handle,
    				'valueHandle' => array('Code' => get_option('woocommerce_currency'))
    			));	 
            }
				
			return $debtor_handle;
		}catch (Exception $exception) {
			logthis("woo_get_debtor_handle_from_economic could not get or create debtor handle: " . $exception->getMessage());
			//$wce_api->debug_client($client);
			return null;
		}
	}
	
	/**
     * Get debtor debtor vat Zone from WooCommerce user object or order object.
     *
     * @access public
     * @param Type of address billing or shipping, WP user object and WC order object
     * @return vatZone string.
     */
	public function woo_get_debtor_vat_zone($type, WP_User $user = NULL, WC_Order $order = NULL){
		$default_country = get_option('woocommerce_default_country');
		$address = $type.'_country';
		logthis('woo_get_debtor_vat_zone running...');
		//logthis($order->$address.' == '.$default_country);
		if(is_object($order)){
			if($order->$address == $default_country){
				return 'HomeCountry';
			}elseif(isset($this->eu[$order->$address])){
				return 'EU';
			}else{
				return 'Abroad';
			}
		}
		if(is_object($user)){
			$userCountry = get_user_meta($user->ID, $address, true);
			if($userCountry == $default_country){
				return 'HomeCountry';
			}elseif(isset($this->eu[$userCountry])){
				return 'EU';
			}else{
				return 'Abroad';
			}
		}
	}
	
	/**
     * Get debtor delivery locations handle from economic
     *
     * @access public
     * @param User object, SOAP client
     * @return debtor_delivery_location_handles object
     */
	public function woo_get_debtor_delivery_location_handles_from_economic(SoapClient &$client, $debtor_handle){
		
		//$debtor_handle = $this->woo_get_debtor_handle_from_economic($user, $client);
		
		if (!isset($debtor_handle) || empty($debtor_handle)) {
			logthis("woo_get_debtor_delivery_location_handles_from_economic no handle found");
			return null;
		}
		
		logthis("woo_get_debtor_delivery_location_handles_from_economic getting delivery locations available for debtor debtor_delivery_location_handles");
		//logthis($debtor_handle);
		$debtor_delivery_location_handles = $client->Debtor_GetDeliveryLocations(array(
		'debtorHandle' => $debtor_handle
		))->Debtor_GetDeliveryLocationsResult;
		
		//logthis("debtor_delivery_location_handles");
		//logthis($debtor_delivery_location_handles);
		
		if (isset($debtor_delivery_location_handles->DeliveryLocationHandle->Id)){
			logthis("woo_get_debtor_delivery_location_handles_from_economic delivery location handle ID: ");
			logthis($debtor_delivery_location_handles->DeliveryLocationHandle->Id);
			return $debtor_delivery_location_handles->DeliveryLocationHandle;
		}
		else {
			$debtor_delivery_location_handle = $client->DeliveryLocation_Create(array(
			'debtorHandle' => $debtor_handle
			))->DeliveryLocation_CreateResult;
			logthis("woo_get_debtor_delivery_location_handles_from_economic delivery location handle: ");
			logthis($debtor_delivery_location_handle);
			return $debtor_delivery_location_handle;
		}
	}
	
	
	 /**
     * Save WooCommerce Order to e-conomic
     *
     * @access public
     * @param product oject, user object, Soap client object, reference order ID and refund flag.
     * @return bool
     */
	public function save_order_to_economic(SoapClient &$client, WP_User $user = NULL, WC_Order $order = NULL, $refund = NULL){
		global $wpdb;
		logthis("save_order_to_economic Getting debtor handle");
		$debtor_handle = $this->woo_get_debtor_handle_from_economic($client, $user, $order);
		//Added for add-on plugin.
		$modified_data = apply_filters( 'wooconomics_order_for_addon', array('client' => $client, 'order' => $order));
		if(is_object($modified_data['order'])){
			$order = $modified_data['order'];
		}
		if(is_object($modified_data['debtor_handle'])){
			$debtor_handle = $modified_data['debtor_handle'];
		}
		//Added for add-on plugin.
		if (!($debtor_handle)) {
			logthis("save_order_to_economic debtor not found, can not create order");
			return false;
		}
		try {
			if(isset($order->order_number) && $order->order_number != NULL){
				$orderId = $order->order_number;
			}else{
				$orderId = $order->id;
			}
			$order_handle = $this->woo_get_order_number_from_economic($client, $order, $this->order_reference_prefix.$orderId, $debtor_handle);

			//$order_handle_array = (array) $order_handle;
			
			if($order_handle === true){
				logthis("save_order_to_economic order is already synced to draft invoice or invoice.");
				return true;
			}
			
			if($order_handle === false){
				logthis("save_order_to_economic order handle creation error.");
				return false;
			}

			
			$countries = new WC_Countries();
			
			/*$address = null;
			$city = null;
			$postalcode = null;
			$country = null;
			
			if (isset($order->shipping_address_1) || !empty($order->shipping_address_1)) {
				$formatted_state = $countries->states[$order->shipping_country][$order->shipping_state];
				$address = trim($order->shipping_address_1 . "\n" . $order->shipping_address_2 . "\n" . $formatted_state);
				$city = $order->shipping_city;
				$postalcode = $order->shipping_postcode;
				$country = $countries->countries[$order->shipping_country];
			} else {
				$formatted_state = $countries->states[$order->billing_country][$order->billing_state];
				$address = trim($order->billing_address_1 . "\n" . $order->billing_address_2 . "\n" . $formatted_state);
				$city = $order->billing_city;
				$postalcode = $order->billing_postcode;
				$country = $countries->countries[$order->billing_country];
			}*/
			
			$formatted_state = $countries->states[$order->billing_country][$order->billing_state];
			$address = trim($order->billing_address_1 . "\n" . $order->billing_address_2 . "\n" . $formatted_state);
			$city = $order->billing_city;
			$postalcode = $order->billing_postcode;
			//$country = $countries->countries[$order->billing_country];
			$country = $order->billing_country;
			
			logthis("save_order_to_economic Order_SetDebtor.");
			$debtorName = $order->billing_company != ''? $order->billing_company : $order->billing_first_name.' '.$order->billing_last_name;
			$client->Order_SetDebtor(array(
				'orderHandle' => $order_handle,
				'valueHandle' => $debtor_handle
			));
			
			logthis("save_order_to_economic Order_SetDebtorName.");
			$client->Order_SetDebtorName(array(
				'orderHandle' => $order_handle,
				'value' => $debtorName
			));
			
			logthis("save_order_to_economic Order_SetDebtorAddress.");
			$client->Order_SetDebtorAddress(array(
				'orderHandle' => $order_handle,
				'value' => $address
			));
			
			logthis("save_order_to_economic Order_SetDebtorCity.");
			$client->Order_SetDebtorCity(array(
				'orderHandle' => $order_handle,
				'value' => $city
			));
			
			logthis("save_order_to_economic Order_SetDebtorCountry.");
			$client->Order_SetDebtorCountry(array(
				'orderHandle' => $order_handle,
				'value' => $country
			));
			
			logthis("save_order_to_economic Order_SetDebtorPostalCode.");
			$client->Order_SetDebtorPostalCode(array(
				'orderHandle' => $order_handle,
				'value' => $postalcode
			));
			
			
			$formatted_state = $countries->states[$order->shipping_country][$order->shipping_state];
			$address = trim($order->shipping_address_1 . "\n" . $order->shipping_address_2 . "\n" . $formatted_state);
			$city = $order->shipping_city;
			$postalcode = $order->shipping_postcode;
			//$country = $countries->countries[$order->shipping_country];
			$country = $order->shipping_country;
			
			logthis("save_order_to_economic Order_SetDeliveryAddress.");
			$client->Order_SetDeliveryAddress(array(
				'orderHandle' => $order_handle,
				'value' => $address
			));
			
			logthis("save_order_to_economic Order_SetDeliveryCity.");
			$client->Order_SetDeliveryCity(array(
				'orderHandle' => $order_handle,
				'value' => $city
			));
			
			logthis("save_order_to_economic Order_SetDeliveryCountry.");
			$client->Order_SetDeliveryCountry(array(
				'orderHandle' => $order_handle,
				'value' => $country
			));
			
			logthis("save_order_to_economic Order_SetDeliveryPostalCode.");
			$client->Order_SetDeliveryPostalCode(array(
				'orderHandle' => $order_handle,
				'value' => $postalcode
			));
			
			
			
			//Add for version 1.9.7 by Alvin
			//Set the currency of the e-conomic order based on the store Currency.
			
			//Changed for version 1.9.17 to support WooCommerce Product Price Based on Countries
			if(isset($order->order_currency) && $order->order_currency != ''){
				$client->Order_SetCurrency(array(
					'orderHandle' => $order_handle,
					'valueHandle' => array('Code' => $order->order_currency)
				));
			}else{
				$client->Order_SetCurrency(array(
					'orderHandle' => $order_handle,
					'valueHandle' => array('Code' => get_option('woocommerce_currency'))
				));
			}
			
			//Added for version 1.9.9.8
			//Set the order date
			$date = new DateTime($order->order_date);
			$client->Order_SetDate(array(
				'orderHandle' => $order_handle,
				'value' => $date->format('c')
			));
			
			//logthis($orderLines);
			
			$orderLines = $client->Order_GetLines(array(
				'orderHandle' => $order_handle,
			))->Order_GetLinesResult;
			
			if(isset($orderLines->OrderLineHandle)){
				if(is_array($orderLines->OrderLineHandle)){
					foreach($orderLines->OrderLineHandle as $orderLine){
						$client->OrderLine_Delete(array(
							'orderLineHandle' => $orderLine,
						));
					}
				}else{
					$client->OrderLine_Delete(array(
						'orderLineHandle' => $orderLines->OrderLineHandle,
					));
				}
			}
			
			logthis("save_order_to_economic call woo_handle_order_lines_to_economic.");			
			$this->woo_handle_order_lines_to_economic($order, $order_handle, $client, $refund);

			//logthis("SELECT * FROM wce_orders WHERE order_id=".$order->id.": ".$wpdb->query ("SELECT * FROM wce_orders WHERE order_id=".$order->id.";"));
		
			if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id.";")){
				$wpdb->update ($wpdb->prefix."wce_orders", array('synced' => 1), array('order_id' => $order->id), array('%d'), array('%d'));
			}else{
				$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 1), array('%d', '%d'));
			}
			return $order_handle;
		} catch (Exception $exception) {
			logthis("save_order_to_economic could not save order: " . $exception->getMessage());
			$this->debug_client($client);
			logthis('Could not create invoice.');
			logthis($exception->getMessage());
			if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_orders WHERE order_id=".$order->id." AND synced=0;")){
				return false;
			}else{
				$wpdb->insert ($wpdb->prefix."wce_orders", array('order_id' => $order->id, 'synced' => 0), array('%d', '%d'));
				return false;
			}
		}
	}
	
	/**
     * Update the payment method and payment status to e-conomic order or invoice.
     *
     * @access public
     * @param SOAP client, Order object
	 * @return order handle if found, otherwise 
	 * Update for version 1.9.9.8 to update payment method and status to e-conomic order or invoice.
     */	
	 public function woo_update_order_payment_to_economic(SoapClient &$client, WC_Order $order){
		 $handle = $this->woo_is_order_synced_already($client, $order);
		 if($handle['type'] == 'order'){
			 logthis('woo_update_order_payment_to_economic: updating order ');
			 $textline1 = __( 'Payment Method:', 'woocommerce' ).' '.$order->payment_method_title.' ('.current_time('Y-m-d').')';
			 $client->Order_SetTextLine1(array(
				'orderHandle' => $handle['handle'],
				'value' => $textline1
			 ));
			 logthis('woo_update_order_payment_to_economic: updated order ');
		 }
		 
		 if($handle['type'] == 'current_invoice'){
			 logthis('woo_update_order_payment_to_economic: updating current_invoice ');
			 $textline1 = __( 'Payment Method:', 'woocommerce' ).' '.$order->payment_method_title.' ('.current_time('Y-m-d').')';
			 $client->CurrentInvoice_SetTextLine1(array(
				'currentInvoiceHandle' => $handle['handle'],
				'value' => $textline1
			 ));
			 logthis('woo_update_order_payment_to_economic: updated current_invoice ');
		 }
	 }

	
	/**
     * Get or Create order number from economic
     *
     * @access public
     * @param User object, SOAP client, debtor_handle
	 * @return order handle if found, otherwise 
	 * Update for version 1.9.9.1 to prevent duplicate order sync.
     */	
	public function woo_get_order_number_from_economic(SoapClient &$client, WC_Order $order, $reference, &$debtor_handle){
		$is_synced = $this->woo_is_order_synced_already($client, $order);
		
		if($is_synced['synced'] === true && $is_synced['type'] === 'order'){
			$economic_order = $is_synced['handle'];
			logthis("woo_get_order_number_from_economic order already exists");
			logthis($economic_order);
			return $economic_order;
		}
		
		if($is_synced['synced'] === false){
			logthis("woo_get_order_number_from_economic order doesn't exists, creating new order!");
			$economic_order = $client->Order_Create(array(
				'debtorHandle' => $debtor_handle
			))->Order_CreateResult;
			if(isset($economic_order->Id) && !empty($economic_order->Id)){
				logthis("woo_get_order_number_from_economic orderId " . $economic_order->Id . " created!");
				$client->Order_SetOtherReference(array(
					'orderHandle' => $economic_order,
					'value' => $reference
				));
                
                logthis("Adding order comments to Text line 2:".$order->customer_message);
                $client->Order_SetTextLine2(array(
    				'orderHandle' => $economic_order,
    				'value' => $order->customer_message
    			 ));
                
				return $economic_order;
			}else{
				logthis("woo_get_order_number_from_economic creating new order failed!");
				return false;
			}
		}	
		
		if($is_synced['synced'] === true && $is_synced['type'] == 'current_invoice'){	
			logthis("woo_get_order_number_from_economic e-conomic order is converted to e-conomic, calling save_invoice_to_economic_function");
			$this->save_invoice_to_economic($client, NULL, $order, $refund = NULL);
			return true;
		}
		
		if($is_synced['synced'] === true && $is_synced['type'] !== 'order'){	
			return true;
		}
		
		
	}

	
	
	/**
     * Get order lines handle
     *
     * @access public
     * @param Order object, Invoice handle object, SOAP client, refund bool
     * @return debtor_handle object
     */	
	public function woo_handle_order_lines_to_economic(WC_Order $order, $order_handle, SoapClient &$client, $refund){
	  logthis("woo_handle_order_lines_to_economic - get all lines");
	
	  foreach ($order->get_items() as $item) {
		//logthis('orderline item');
		//logthis($item);
		$product = $order->get_product_from_item($item);
		//$line = $lines[$this->woo_get_product_sku($product)];
		if(isset($product) && !empty($product)){
			$order_line_handle = null;
			$order_line_handle = $this->woo_create_orderline_handle_at_economic($order_handle, $this->woo_get_product_sku($product), $client);
		
			logthis("woo_handle_order_lines_to_economic updating qty on id: " . $order_line_handle->Id . " number: " . $order_line_handle->Number);
			$quantity = ($refund) ? $item['qty'] * -1 : $item['qty'];
			
			$client->OrderLine_SetQuantity(array(
			  'orderLineHandle' => $order_line_handle,
			  'value' => $quantity
			));
			$client->OrderLine_SetUnitNetPrice(array(
			  'orderLineHandle' => $order_line_handle,
			  'value' => $item['line_subtotal']/$item['qty']
			));
			
			$discount = $item['line_subtotal']-$item['line_total'];
			if($discount != 0){
				$discount = ($discount*100)/$item['line_subtotal'];
				$client->OrderLine_SetDiscountAsPercent(array(
				  'orderLineHandle' => $order_line_handle,
				  'value' => $discount
				));
			}

			//Added for add-on plugin.
			apply_filters( 'wooconomics_set_orderline_inventory_location', array('client' => $client, 'orderLineHandle' => $order_line_handle, 'type' => 'orderLineHandle'));
			//Added for add-on plugin.

			logthis("woo_handle_order_lines_to_economic updated line");
		}
	  }
	  
		$shippingItem = reset($order->get_items('shipping'));
		logthis('shippingItem:');
		logthis($shippingItem);
		if(isset($shippingItem['method_id'])){
			logthis("woo_handle_order_lines_to_economic adding Shipping line");
			if(strlen($shippingItem['method_id']) > 25){
				$shippingID = substr($shippingItem['method_id'], 0, 24);
			}else{
				$shippingID = $shippingItem['method_id'];
			}
			$order_line_handle = null;
			$order_line_handle = $this->woo_create_orderline_handle_at_economic($order_handle, $shippingID , $client);
			logthis("woo_handle_order_lines_to_economic updating qty on id: " . $order_line_handle->Id . " number: " . $order_line_handle->Number);
			$quantity = ($refund) ? -1 : 1;
			$client->OrderLine_SetQuantity(array(
			'orderLineHandle' => $order_line_handle,
			'value' => $quantity
			));
			$client->OrderLine_SetUnitNetPrice(array(
			  'orderLineHandle' => $order_line_handle,
			  'value' => $shippingItem['cost']
			));
			
			logthis("woo_handle_order_lines_to_economic updated shipping line");
		}
		

		$coupon = reset($order->get_items('coupon'));
		//logthis('coupon');
		//logthis($coupon);
		if(isset($coupon['name'])){
			logthis("woo_handle_order_lines_to_economic adding Coupon line");
			if(strlen($coupon['name']) > 25){
				$couponID = substr($coupon['name'], 0, 24);
			}else{
				$couponID = $coupon['name'];
			}
			$order_line_handle = null;
			$order_line_handle = $this->woo_create_orderline_handle_at_economic($order_handle, $couponID , $client);
			logthis("woo_handle_order_lines_to_economic updating qty on id: " . $order_line_handle->Id . " number: " . $order_line_handle->Number);
			$quantity = ($refund) ? -1 : 1;
			$client->OrderLine_SetQuantity(array(
			'orderLineHandle' => $order_line_handle,
			'value' => $quantity
			));
			/*$client->OrderLine_SetUnitNetPrice(array(
			  'orderLineHandle' => $order_line_handle,
			  'value' => -$coupon['discount_amount']
			));*/
			logthis("woo_handle_order_lines_to_economic updated coupon line");
		}
	}
	
	
	/**
     * Get order lines to e-conomic 
     *
     * @access public
     * @param 
     * @return array log
     */
	public function woo_create_orderline_handle_at_economic($order_handle, $product_id, SoapClient &$client){
		
		$product_handle = $client->Product_FindByNumber(array(
			'number' => $product_id
		))->Product_FindByNumberResult;
		
		$orderline_handle = $client->OrderLine_Create(array(
			'orderHandle' => $order_handle
		))->OrderLine_CreateResult;
		
		logthis("woo_create_orderline_handle_at_economic added line id: " . $orderline_handle->Id . " number: " . $orderline_handle->Number . " product_id: " . $product_id);
		
		$client->OrderLine_SetProduct(array(
			'orderLineHandle' => $orderline_handle,
			'valueHandle' => $product_handle
		));
		$product = $client->Product_GetData(array(
			'entityHandle' => $product_handle
		))->Product_GetDataResult;
		$client->OrderLine_SetDescription(array(
			'orderLineHandle' => $orderline_handle,
			'value' => $product->Name
		));
		$client->OrderLine_SetUnitNetPrice(array(
			'orderLineHandle' => $orderline_handle,
			'value' => $product->SalesPrice
		));
		
		logthis("woo_create_orderline_handle_at_economic added product to line ");
		return $orderline_handle;
	}
	
	
	
	/**
     * Check if the WooCommerce order is already synced as CurrentInvoice or Invoice, e-conomic order is fine for updating.
     *
     * @access public
     * @param 
     * @return array containing boolean flag for synced or no synced and handle if found.
     */
	 public function woo_is_order_synced_already(SoapClient &$client, WC_Order $order){
		 
		$return = array('synced'=> false, 'type' => NULL, 'handle'=> NULL);
		 
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(isset($order->order_number) && $order->order_number != NULL){
			$orderId = $order->order_number;
		}else{
			$orderId = $order->id;
		}
		
		try{			
			logthis('woo_is_order_synced_already: finding current invoice handle by other reference.');
			$current_invoice_handle = $client->CurrentInvoice_FindByOtherReference(array(
				'otherReference' => $this->order_reference_prefix.$orderId
			))->CurrentInvoice_FindByOtherReferenceResult;
			
			$current_invoice_handle_array = (array) $current_invoice_handle;
            
            logthis($this->order_reference_prefix.$orderId);
			
			if(!empty($current_invoice_handle_array)) {
				logthis('woo_is_order_synced_already: Current Invoice handle found.');
				$return['synced'] = true;
				$return['type'] = 'current_invoice';
				$return['handle'] = $current_invoice_handle->CurrentInvoiceHandle;
				return $return;
			}
			
			logthis('woo_is_order_synced_already: finding invoice handle by other reference.');
			$invoice_handle = $client->Invoice_FindByOtherReference(array(
				'otherReference' => $this->order_reference_prefix.$orderId
			))->Invoice_FindByOtherReferenceResult;
			
			$invoice_handle_array = (array )$invoice_handle;		
			
			if(!empty($invoice_handle_array)) {
				logthis('woo_is_order_synced_already: Invoice handle found.');
				$return['synced'] = true;
				$return['type'] = 'invoice';
				$return['handle'] = $invoice_handle->InvoiceHandle;
				return $return;
			}
			
			logthis('woo_is_order_synced_already: finding order handle by other reference.');
			$economic_order_handle = $client->Order_FindByOtherReference(array(
				'otherReference' => $this->order_reference_prefix.$orderId
			))->Order_FindByOtherReferenceResult; 
			
			$economic_order_handle_array = (array) $economic_order_handle;
			
			if(!empty($economic_order_handle_array)) {
				logthis('woo_is_order_synced_already: Order handle found.');
				$return['synced'] = true;
				$return['type'] = 'order';
				$return['handle'] = $economic_order_handle->OrderHandle;
				return $return;
			}
			
			return $return;
		}catch (Exception $exception) {
			logthis('woo_is_order_synced_already: Something went wrong with the request.');
			$this->debug_client($client);
			logthis($exception->getMessage);
			$return = array('synced'=> false, 'type' => NULL, 'handle'=> NULL);
			return $return;
		}
		
	 }
	
	
	/**
     * Sync WooCommerce orders to e-conomic 
     *
     * @access public
     * @param 
     * @return array log
     */
	public function sync_orders(){
		global $wpdb;
		$options = get_option('woocommerce_economic_general_settings');
		$client = $this->woo_economic_client();
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => 'fail', 'msg' => 'Could not create e-conomic client, please try again later!' ));
			return $sync_log;
		}
		$orders = array();
		$sync_log = array();
		$sync_log[0] = true;
		logthis("sync_orders starting...");
        $unsynced_orders = $wpdb->get_results("SELECT * from ".$wpdb->prefix."wce_orders WHERE synced = 0");

		foreach ($unsynced_orders as $order){
			$orderId = $order->order_id;
			array_push($orders, new WC_Order($orderId));
		}
		
		if($this->activate_oldordersync == "on"){
			$all_unsynced_orders = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."posts WHERE ID NOT IN (SELECT order_id FROM ".$wpdb->prefix."wce_orders) AND post_type='shop_order' AND post_status != 'trash' AND post_status != 'wc-failed' AND post_status != 'wc-cancelled'");
			foreach ($all_unsynced_orders as $order){
				$orderId = $order->ID;
				array_push($orders, new WC_Order($orderId));
			}
		}
		
		if(!empty($orders)){
			foreach ($orders as $order) {
				logthis('sync_orders Order ID: ' . $order->id);
				if($options['initiate-order'] == 'status_based'){
					if($options['initiate-order-status-'.$order->get_status()] != 'on'){
						$sync_log[0] = false;
						array_push($sync_log, array('status' => __('skipped', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order skipped from syncing, because the order status "'.$order->get_status().'" is not selected for sync in "Initiate order sync"' , 'woocommerce-e-conomic-integration')));
						continue;
					}
				}else{
					
				}
				
				if($order->customer_user != 0){
					$user = new WP_User($order->customer_user);
				}else{
					$user = NULL;
				}
				$this->save_customer_to_economic($client, $user, $order);
				if($order->customer_user != 0){
					$user = new WP_User($order->customer_user);
				}else{
					$user = NULL;
				}				
				
				if($order->payment_method == 'economic-invoice'){
					logthis("sync_orders syncing WC order for e-conomic payment.");
					if($this->economic_checkout == 'order'){
						if($this->save_order_to_economic($client, $user, $order, false)){
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order synced successfully', 'woocommerce-e-conomic-integration') ));
						}else{
							$sync_log[0] = false;
							array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Sync failed, please try again later!' , 'woocommerce-e-conomic-integration')));
						}
					}
					
					if($this->economic_checkout == 'draft invoice' || $this->economic_checkout == 'invoice'){
						if($this->save_invoice_to_economic($client, $user, $order, false)){
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order synced successfully' ), 'woocommerce-e-conomic-integration'));
						}else{
							$sync_log[0] = false;
							array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Sync failed, please try again later!' , 'woocommerce-e-conomic-integration')));
						}
							
						if($this->economic_checkout == 'invoice'){
							if($this->send_invoice_economic($client, $order)){
								logthis("sync_orders invoice for order: " . $order_id . " is sent to customer.");
							}else{
								logthis("sync_orders invoice for order: " . $order_id . " sending failed!");
							}
						}
					}
				}else{
					logthis("sync_orders syncing WC order for payment method except e-conomic.");
					if($this->other_checkout == 'do nothing'){
						logthis("sync_orders order: " . $order_id . " is not synced synced with economic because do nothing is selected for e-conomic payment.");
						array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order not synced, because Other checkout is set to "Do nothing"', 'woocommerce-e-conomic-integration') ));
						continue; //Check if the payment is not e-conomic and all order sync is active, if not breaks this iteration and continue with other orders.
					}
					
					if($this->other_checkout == 'order'){
						if($this->save_order_to_economic($client, $user, $order, false)){
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order synced successfully', 'woocommerce-e-conomic-integration') ));
						}else{
							$sync_log[0] = false;
							array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Sync failed, please try again later!' , 'woocommerce-e-conomic-integration')));
						}
					}
					
					if($this->other_checkout == 'draft invoice' || $this->other_checkout == 'invoice'){
						if($this->save_invoice_to_economic($client, $user, $order, false)){
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Order synced successfully' ), 'woocommerce-e-conomic-integration'));
						}else{
							$sync_log[0] = false;
							array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'order_id' => $order->id, 'msg' => __('Sync failed, please try again later!' , 'woocommerce-e-conomic-integration')));
						}
							
						if($this->other_checkout == 'invoice'){
							if($this->send_invoice_economic($client, $order)){
								logthis("sync_orders invoice for order: " . $order_id . " is sent to customer.");
							}else{
								logthis("sync_orders invoice for order: " . $order_id . " sending failed!");
							}
						}
					}	
				}
			}
		}else{
			$sync_log[0] = true;
			array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'order_id' => '', 'msg' => __('All orders were already synced!', 'woocommerce-e-conomic-integration') ));
		}
		
		$client->Disconnect();
		logthis("sync_orders ending...");
		return $sync_log;
	}
	
	
	 /**
     * Save WooCommerce Product to e-conomic
     *
     * @access public
     * @param product oject
     * @return bool
     */
	
	public function save_product_to_economic(WC_Product $product, SoapClient &$client){
		if(!$client){
			return false;
		}

		$options = get_option('woocommerce_economic_general_settings');
		
		
		
		logthis("save_product_to_economic syncing product - sku: " . $product->sku . " title: " . $product->get_title());
		try	{
			$product_sku = $this->woo_get_product_sku($product);
			//logthis("save_product_to_economic - trying to find product in economic with product number: ".$product_sku);
			
			// Find product by number
			logthis('Finding product by number: '.$product_sku);
			$product_handle = $client->Product_FindByNumber(array(
				'number' => $product_sku
			))->Product_FindByNumberResult;
			
			logthis('--product_handle--');
			logthis($product_handle);
			
			$productGroup = get_post_meta( $product->id, 'productGroup', true );
			if($productGroup == '' || $productGroup == NULL){
				logthis('save_product_to_economic productGroup is used from the plugin settings: '. $this->product_group);
				$productGroup = $this->product_group;
			}else{
				logthis('save_product_to_economic productGroup is used from the product meta: '. $productGroup);
				 if ($product_handle && !empty($product_handle)){
					  logthis('save_product_to_economic setting productGroup for the e-conomic product.');
					  $client->Product_SetProductGroup(array(
						'productHandle' => $product_handle,
						'valueHandle' => array('Number' => $productGroup)
					 ));
				 }
			}
			
			// Create product with name
			if (!$product_handle) {
				$productGroupHandle = $client->ProductGroup_FindByNumber(array(
					'number' => $productGroup
				))->ProductGroup_FindByNumberResult;
				$product_handle = $client->Product_Create(array(
					'number' => $product_sku,
					'productGroupHandle' => $productGroupHandle,
					'name' => $product->get_title()
				))->Product_CreateResult;
				logthis($product_handle);
				logthis("save_product_to_economic - product created:" . $product->get_title());
			}else{
				$client->Product_SetProductGroup(array(
					'productHandle' => $product_handle,
					'valueHandle' => array('Number' => $productGroup)
				 ));
			}
			
			// Get product data
			$product_data = $client->Product_GetData(array(
				'entityHandle' => $product_handle
			))->Product_GetDataResult;
			
			
			//logthis($product_data);
			//return true;

			//logthis($product_data->DepartmentHandle);
			//logthis($product_data->DistrubutionKeyHandle);
			if($this->product_sync != "on"){
				logthis("Product sync exiting, because product sync is not activated");
				
				//Update InStock from e-conomic to woocommerce
				//Added for addon
				$modified_product = apply_filters( 'wooconomics_before_product_stock_update', $product );
				if(is_object($modified_product)){
					$product = $modified_product;
				}
				if($product->managing_stock() && $this->product_stock_sync == "on"){
					($product_data->InStock >= 0 || $product_data->InStock == '') ? $product->set_stock($product_data->InStock) : logthis('Product stock not updated.');
					logthis('Product: '.$product->get_title().' Stock updated to '.$product_data->InStock);
				}else{
					if($this->product_stock_sync != "on"){
						logthis('Product: '.$product->get_title().' Stock sync is disabled in plugin settings!');
					}else{
						logthis('Product: '.$product->get_title().' Stock management disabled');
					}
				}
				//Added for addon
				$product = apply_filters( 'wooconomics_after_product_stock_update', $product );
				return true;
			}else{
				// Update product data
				
				
				$Company = $client->Company_Get()->Company_GetResult;
				$Company_GetBaseCurrency = $client->Company_GetBaseCurrency(array(
					'companyHandle' => $Company
				))->Company_GetBaseCurrencyResult;
				//logthis('Company_GetBaseCurrency:');
				//logthis($Company_GetBaseCurrency);
				
				if($Company_GetBaseCurrency->Code == get_option('woocommerce_currency')){
					$sales_price = $product->get_price_excluding_tax(1, $product->get_price());
				}else{
					$sales_price = $client->Product_GetSalesPrice(array('productHandle'  => $product_handle))->Product_GetSalesPriceResult;
				}
				
				logthis("Sales Price:".$sales_price);
				
				$client->Product_UpdateFromData(array(
				'data' => (object)array(
				'Handle' => $product_data->Handle,
				'Number' => $product_data->Number,
				'ProductGroupHandle' => $product_data->ProductGroupHandle,
				'Name' => $product->get_title(),
				'Description' => $this->woo_economic_product_content_trim($product->post->post_content, 255),
				'BarCode' => "",
				//'SalesPrice' => (isset($product->price) && !empty($product->price) ? $product->price : 0.0),
				'SalesPrice' => (isset($sales_price) && !empty($sales_price) ? $sales_price : 0.0),
				'CostPrice' => (isset($product_data->CostPrice) ? $product_data->CostPrice : 0.0),
				'RecommendedPrice' => $product_data->RecommendedPrice,
				/*'UnitHandle' => (object)array(
				'Number' => 1
				),*/
				'IsAccessible' => true,
				'Volume' => $product_data->Volume,
				//'DepartmentHandle' => isset($product_data->DepartmentHandle) ? $product_data->DepartmentHandle : '',
				//'DistributionKeyHandle' => isset($product_data->DistrubutionKeyHandle) ? $product_data->DistrubutionKeyHandle : '',
				'InStock' => $product_data->InStock,
				'OnOrder' => $product_data->OnOrder,
				'Ordered' => $product_data->Ordered,
				'Available' => $product_data->Available)))->Product_UpdateFromDataResult;
				
				//Added in version 1.9.9.9.1 by Alvin for updaing the product price in store currency settings.
				if($Company_GetBaseCurrency->Code != get_option('woocommerce_currency')){
					$sales_price = $product->get_price_excluding_tax(1, $product->get_price());
					$productPriceHandle = $client->ProductPrice_FindByProductAndCurrency(array(
						'productHandle'  => $product_handle,
						'currencyHandle' => array('Code' => get_option('woocommerce_currency')),
					))->ProductPrice_FindByProductAndCurrencyResult;
					
					if(isset($productPriceHandle) && !empty($productPriceHandle)){
						logthis('productPriceHandle:');
						logthis($productPriceHandle);
						$client->ProductPrice_SetPrice(array(
							'productPriceHandle'  => $productPriceHandle,
							'value'			 => (isset($sales_price) && !empty($sales_price) ? $sales_price : 0.0)
						));
					}else{
						logthis('productPriceHandle not found, creating product price.');
						$client->ProductPrice_Create(array(
							'productHandle'  => $product_handle,
							'currencyHandle' => array('Code' => get_option('woocommerce_currency')),
							'price'			 => (isset($sales_price) && !empty($sales_price) ? $sales_price : 0.0)
						));
					}
				}
				
				//Update InStock from e-conomic to woocommerce
				//Added for addon
				$modified_product = apply_filters( 'wooconomics_before_product_stock_update', $product );
				if(is_object($modified_product)){
					$product = $modified_product;
				}
				if($product->managing_stock() && $this->product_stock_sync == "on"){
					($product_data->InStock >= 0 || $product_data->InStock =='') ? $product->set_stock($product_data->InStock) : logthis('Product stock not updated.');
					logthis('Product: '.$product->get_title().' Stock updated to '.$product_data->InStock);
				}else{
					if($this->product_stock_sync != "on"){
						logthis('Product: '.$product->get_title().' Stock sync is disabled in plugin settings!');
					}else{
						logthis('Product: '.$product->get_title().' Stock management disabled');
					}
				}
				//Added for addon
				$product = apply_filters( 'wooconomics_after_product_stock_update', $product );
			}			
			logthis("save_product_to_economic - product updated : " . $product->get_title());
            //Added for WooConomics 1.9.24
            update_post_meta($product->id, 'economic_sync', time());
            //Added for WooConomics 1.9.24
			return true;
		} catch (Exception $exception) {
			logthis("save_product_to_economic could not create product: " . $exception->getMessage());
			$this->debug_client($client);
			logthis($exception->getMessage);
			return false;
		}
	}
	
	
	 /**
	 * Removes tags and shortens the string to length
	 */
	 public function woo_economic_product_content_trim($str, $max_len)
	 {
	  logthis("woo_economic_product_content_trim '" . $str . "'");
	  $result = strip_tags($str);
	  if (strlen($result) > $max_len)
		$result = substr($result, 0, $max_len-1);
        
        $result = iconv("UTF-8", "UTF-8//TRANSLIT", $result);
        
        if($result == '' || $result == false){
            logthis("Product description has invalid UTF-8 characters, syncing product description is skipped");
        }
	
        logthis("woo_economic_product_content_trim result: '" . $result . "'");
	
	  return $result;
	 }


	/**
     * Sync WooCommerce Products to e-conomic 
     *
     * @access public
     * @param 
     * @return array log
     */
	 
	 public function sync_products(){
		$client = $this->woo_economic_client();
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => 'fail', 'msg' => 'Could not create e-conomic client, please try again later!' ));
			return $sync_log;
		}
		$products = array();
		$sync_log = array();
		$sync_log[0] = true;
		$args = array('post_type' => array('product'), 'post_status' => array( 'publish', 'future', 'private' ), 'nopaging' => true, 'fields' => 'ids');
		$product_ids = new WP_Query($args);
		//$posts = $product_query->get_posts();
		foreach ($product_ids->posts as $key=>$post_id) {

			array_push($products, $post_id);
		}
		
		//Added for 1.9.5 update by Alvin
		$variation_args = array('post_type' => array('product_variation'), 'nopaging' => true, 'fields' => 'ids');
		$product_variation_ids = new WP_Query($variation_args);
		//$variation_posts = $product_variation_query->get_posts();
		foreach ($product_variation_ids->posts as $key=>$variation_post_id) {
			$variation_parent_post_id = wp_get_post_parent_id( $variation_post_id );
			$variation_parent_post = get_post($variation_parent_post_id);
			if($variation_parent_post->post_status == 'publish' || $variation_parent_post->post_status == 'future' || $variation_parent_post->post_status == 'private'){
				array_push($products, $variation_post_id);
			}
		}
		
		logthis("sync_products starting...");
		foreach ($products as $key=>$productID) {
			$product = new WC_Product($productID);
            
            //Added for WooConomics 1.9.24
		    $last_sync = get_post_meta($productID, 'economic_sync', true);
            $diff = time() - $last_sync;
            if($diff <= 600 && $last_sync != ''){
                $diff = floor($diff/60);
                $message = 'Product with SKU:'.$product->sku.' is skipped, because its been synced '.$diff.' mins before, try after 10 mins.';
                logthis($message);
                array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __($message, 'woocommerce-e-conomic-integration') ));
                continue;
            }
            //Added for WooConomics 1.9.24
            
			logthis('sync_products Product ID: ' . $product->id);
			logthis('sync_products saving product: ' . $product->get_title() . " sku: " . $product->sku);
			logthis('Product SKU: '. $product->sku );
			logthis('Product Title: '.$product->get_title());
			$title = $product->get_title();
			if (isset($product->sku) && !empty($product->sku) && isset($title) && !empty($title)) {
				if($this->save_product_to_economic($product, $client)){
					if($this->product_sync != "on"){
						if($product->managing_stock()){
							$message = 'Product sync: Disabled! Use "Activate product sync" settings to enable it.';
							$message = $this->product_stock_sync == "on" ? $message.'<br> Product stock sync: Successfull!' : '<br> Product stock sync disabled!';
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __($message, 'woocommerce-e-conomic-integration') ));
						}else{
							$message = 'Product sync: Disabled! Use "Activate product sync" settings to enable it. <br> Product stock sync: Stock management disabled, Stock management can be enabled at Product->Inventory.';
							$message = $this->product_stock_sync == "on" ? $message.'<br> Product stock sync: Successfull!' : $message.'<br> Product stock sync disabled!';
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __('', 'woocommerce-e-conomic-integration') ));
						}
					}else{
						if($product->managing_stock()){
							$message = 'Product sync: Successful!';
							$message = $this->product_stock_sync == "on" ? $message.'<br> Product stock sync: Successfull!' : '<br> Product stock sync disabled!';
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __($message, 'woocommerce-e-conomic-integration') ));
						}else{
							$message = 'Product sync: Successful! <br> Product stock sync: Stock management disabled, Stock management can be enabled at Product->Inventory.';
							$message = $this->product_stock_sync == "on" ? $message.'<br> Product stock sync: Successfull!' : $message.'<br> Product stock sync disabled!';
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __($message, 'woocommerce-e-conomic-integration') ));
						}
					}
				}else{
					$sync_log[0] = false;
					array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => $product->get_title(), 'msg' => __('Product not synced, someting went wrong. Please try product sync after some time!', 'woocommerce-e-conomic-integration') ));
				}
			} else {
				logthis("Could not sync product: '". $product->get_title() ."' and id: '".$product->id."' to e-conomic. Please update it with:");
				if (!isset($product->sku) || empty($product->sku)){
				  logthis("SKU");
				  $sync_log[0] = false;
				  array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'sku' => '', 'name' => $product->get_title(), 'msg' => __('Product not synced, SKU is empty!', 'woocommerce-e-conomic-integration') ));
				}
				if (!isset($title) || empty($title)){
				  logthis("Title");
				  $sync_log[0] = false;
				  array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'sku' => $product->sku, 'name' => '', 'msg' => __('Product not synced, product title is empty!', 'woocommerce-e-conomic-integration') ));
				}
			}
		}
		
		$client->Disconnect();
		logthis("sync_products ending...");
		return $sync_log;
	 }
	 
	 
	 
	 /**
     * Sync WooCommerce Products from e-conomic to WooCommerce.
     *
     * @access public
     * @param 
     * @return array log
     */
	 
	public function sync_products_ew($productNo = NULL){
		update_option('woo_save_object_to_economic', false);
		global $wpdb;
		$client = $this->woo_economic_client();
		$sync_log = array();
		$sync_log[0] = true;
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'msg' => __('Could not create e-conomic client, please try again later!', 'woocommerce-e-conomic-integration') ));
			return $sync_log;
		}
		$product_handles = array();
		//Added for version 1.9.9.12
		if($productNo == NULL){
		
			$products = $client->Product_GetAll()->Product_GetAllResult;
			if($products && $products->ProductHandle)
			{
				///split the handles by 1000 to reduce the API query load
				$arrProductHandle=array_chunk($products->ProductHandle, 1000);
				try
				{
					foreach($arrProductHandle as $indSplit=>$ProductHandles)
					{
						$tmpProduct=$client->Product_GetDataArray(array('entityHandles' => $ProductHandles))->Product_GetDataArrayResult;
						foreach($tmpProduct->ProductData as $product)
						{
							$product_handles[$product->Number] = $product;
						}
					}
				}
				catch(Exception $e)
				{
					die($e);
				}
			}
			//logthis($products);
			
			/*$product_handles = array();
			
			foreach($products->ProductHandle as $product){
				$product_handles[$product->Number] = $client->Product_GetProductGroup(array('productHandle' => $product))->Product_GetProductGroupResult;
			}*/
		}else{
			logthis('Finding product by number: '.$productNo);
			$Product_GetData = $client->Product_GetData(array(
				'entityHandle' => array('Number' => $productNo)
			))->Product_GetDataResult;
			
			logthis('--Product_GetData--');
			//logthis($Product_GetData);
			
			$product_handles[$productNo] = $Product_GetData;
		}
		
		logthis('product_handles:');
		//logthis($product_handles);
		
		//Added for version 1.9.9.12
        if($product_handles){
			foreach($product_handles as $product_number => $objProduct){
				$group=$objProduct->ProductGroupHandle;
				
				//Added for version 1.9.16
				if($group->Number != $this->product_group){
					logthis('sync_products_ew Produce groupe: '.$group->Number.' is not selected for sync, only the product group: '.$this->product_group.' is selected for sync');
					continue;
				}
				//Added for version 1.9.16
				
				//Added for version 1.9.9.12 and updated on 1.9.9.14
				$sku = str_replace($this->product_offset, '', $product_number);
				//Added for version 1.9.9.12 and updated on 1.9.9.14
				
				$product_name = $objProduct->Name;
				
				
				$product_post_ids = $wpdb->get_results("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key = '_sku' AND meta_value = '".$sku."'", OBJECT_K );
			
				if(!empty($product_post_ids)){
					foreach($product_post_ids as $product_post_id){
						$product_id = $product_post_id->post_id;
					}
					if(get_post_status( $product_id ) == 'trash'){
						continue;
					}
				}else{
					$product_id = NULL;
				}
				
				logthis('objProduct:');
				//logthis($objProduct);
				
				$product_data = $objProduct;
				
				//logthis($product_data);
				//Added for version 1.9.9.12
				$sku = str_replace($this->product_offset, '', $product_number);
				//Added for version 1.9.9.12
				if($product_id != NULL){
					logthis('update product : '.$product_number);
					$product = new WC_Product($product_id);
					if($this->product_sync == "on"){
						$post = array(
							'ID'		   => $product_id,
							'post_content' => $product_data->Description != ''? $product_data->Description : $product_data->Name,
							'post_title'   => $product_data->Name,
						);
						
						$post_id = wp_update_post( $post, true );
						if (is_wp_error($post_id)) {
							$errors = $post_id->get_error_messages();
							foreach ($errors as $error) {
								logthis($error);
							}
						}
						update_post_meta( $post_id, '_sku', $sku );
						update_post_meta( $post_id, '_regular_price', (string) $product_data->SalesPrice );
						update_post_meta( $post_id, 'productGroup', $group->Number );
						//update_post_meta( $post_id, '_sale_price', (int) $product_data->SalesPrice );
						$productStock = (int)$product_data->InStock;
						if($product->managing_stock() && $this->product_stock_sync == "on"){
							if( $productStock > 0){
								logthis('Product stock updated to: '.$productStock);
								$product->set_stock($productStock);
								update_post_meta( $post_id, '_stock_status', 'instock' );
							}else{
								$product->set_stock(0);
								update_post_meta( $post_id, '_stock_status', 'outofstock' );
							}
							logthis('Product: '.$product->get_title().' Stock updated to '.$productStock);
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product_number, 'name' => $product_data->Name, 'msg' => __('Product sync: Successful! <br> Product stock sync: Successfull!', 'woocommerce-e-conomic-integration') ));
						}else{
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $product_number, 'name' => $product_data->Name, 'msg' => __('Product sync: Successful! <br> Product stock sync: Stock management disabled, Stock management can be enabled at Product->Inventory.', 'woocommerce-e-conomic-integration') ));
						}
					}else{
						if($product->managing_stock() && $this->product_stock_sync == "on"){
							logthis($product_data);
							($productStock >=0 || $productStock =='') ? $product->set_stock($productStock) : logthis('Product stock not updated.');
							logthis('Product: '.$product->get_title().' Stock updated to '.$productStock);
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $sku, 'name' => $product_data->Name, 'msg' => __('Product sync: Disabled! Use "Activate product sync" settings to enable it. <br> Product stock sync: Successfull!', 'woocommerce-e-conomic-integration') ));
						}else{
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $sku, 'name' => $product_data->Name, 'msg' => __('Product sync: Disabled! Use "Activate product sync" settings to enable it. <br> Product stock sync: Stock management disabled, Stock management can be enabled at Product->Inventory.', 'woocommerce-e-conomic-integration') ));
						}
					}
				}else{
					//Added for version 1.9.9.12
					if($productNo != NULL){
						logthis('wp_ajax_nopriv_sync_products_ew_webhook tyring to create new product, this is not allowed yet! Only updating an existing product works!');
						continue;
					}
					//Added for version 1.9.9.12
					logthis('add product : '.$product_number);
					$post = array(
						'post_status'  => 'publish',
						'post_type'    => 'product',
						'post_title'   => $product_data->Name,
						'post_content' => $product_data->Description != ''? $product_data->Description : $product_data->Name,
						'post_excerpt' => $product_data->Description != ''? $product_data->Description : $product_data->Name,
					);
					
					$post_id = wp_insert_post( $post, true );
					if (is_wp_error($post_id)) {
						$errors = $post_id->get_error_messages();
						foreach ($errors as $error) {
							logthis('Product creation error');
							logthis($error);
						}
						continue;
					}
					$product = new WC_Product($post_id);
					update_post_meta( $post_id, '_sku', $sku );
					update_post_meta( $post_id, '_regular_price', (string) $product_data->SalesPrice );
					update_post_meta( $post_id, 'productGroup', $group->Number );
					//update_post_meta( $post_id, '_sale_price', (int) $product_data->SalesPrice );
					$productStock = (int)$product_data->InStock;
					if($productStock > 0){
						$product->manage_stock = 'yes';
						logthis('Product stock updated to: '.$productStock);
						$product->set_stock($productStock);
						update_post_meta( $post_id, '_stock_status', 'instock' );
						$product->manage_stock = 'no';
					}else{
						$product->set_stock(0);
						update_post_meta( $post_id, '_stock_status', 'outofstock' );
					}
	
					array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $sku, 'name' => $product_data->Name, 'msg' => __('Product sync: Successful! <br> Product stock sync: Successfull!', 'woocommerce-e-conomic-integration') ));
				}
			}
		}
		update_option('woo_save_object_to_economic', true);
		return $sync_log;
	}
	 
	 /**
     * Save WooCommerce Product to e-conomic
     *
     * @access public
     * @param product oject
     * @return bool
     */
	 
	 public function save_customer_to_economic(SoapClient &$client, WP_User $user = NULL, WC_Order $order = NULL){
	  logthis("save_customer_to_economic creating client");
	  global $wpdb;	
	  try {
		$debtorHandle = $this->woo_get_debtor_handle_from_economic($client, $user, $order);
		
		if (isset($debtorHandle)) {
			logthis("save_customer_to_economic woo_get_debtor_handle_from_economic handle returned: " . $debtorHandle->Number);
			
			$debtor_delivery_location_handle = $this->woo_get_debtor_delivery_location_handles_from_economic($client, $debtorHandle);
			
			foreach ($this->user_fields as $meta_key) {
				$this->woo_save_customer_meta_data_to_economic($client, $meta_key, $order ? $order->$meta_key: get_user_meta($user->ID, $meta_key, true), $debtorHandle, $debtor_delivery_location_handle, $user, $order);
			}
			
			if(is_object($order)){
				$email = $order->billing_email;
			}
			
			if(is_object($user)){
				$email = $user->get('billing_email');
			}
			
			logthis("save_customer_to_economic customer synced for email: " . $email);
			
			if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email='".$email."';")){
				$wpdb->update ($wpdb->prefix."wce_customers", array('synced' => 1, 'customer_number' => $debtorHandle->Number, 'email' => $email), array('email' => $email), array('%d', '%d', '%s'), array('%s'));
			}else{
				$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $user->ID, 'customer_number' => $debtorHandle->Number, 'email' => $email, 'synced' => 1), array('%d', '%s', '%s', '%d'));
			}
			return true;
		}else{
			logthis("save_customer_to_economic debtor not found.");
			return false;
		}
	  } catch (Exception $exception) {
		logthis("save_customer_to_economic could not save user to e-conomic: " . $exception->getMessage());
		$this->debug_client($client);
		logthis("Could not create user.");
		logthis($exception->getMessage());
		if($wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email=".$email." AND synced=0;")){
			return false;
		}else{
			$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $user->ID, 'customer_number' => '0', 'email' => $email, 'synced' => 0), array('%d', '%s', '%s', '%d'));
			return false;
		}
	  }
	}
	
	/**
     * Save customer meta data to economic
     *
     * @access public
     * @param user object, $meta_key, $meta_value
     * @return void
     */
	public function woo_save_customer_meta_data_to_economic(SoapClient &$client, $meta_key, $meta_value, $debtor_handle, $debtor_delivery_location_handle, WP_User $user = NULL, WC_Order $order = NULL){
	  logthis("woo_save_customer_meta_data_to_economic updating client");
	  //logthis($debtor_handle);
	  //logthis($debtor_delivery_location_handle);
	  $user_id = $user ?  $user->get('ID') : NULL;
	  if (!isset($debtor_handle)) {
		logthis("woo_save_customer_meta_data_to_economic debtor not found, can not update meta");
		return;
	  }
	  try {
	
		if ($meta_key == 'billing_phone') {
		  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
		  $client->Debtor_SetTelephoneAndFaxNumber(array(
			'debtorHandle' => $debtor_handle,
			'value' => $meta_value
		  ));
		} elseif ($meta_key == 'billing_email') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $client->Debtor_SetEmail(array(
				'debtorHandle' => $debtor_handle,
				'value' => $meta_value
			  ));
	
		} elseif ($meta_key == 'billing_country') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $countries = new WC_Countries();
			  $country = $countries->countries[$meta_value];
			  logthis("woo_save_customer_meta_data_to_economic country: " . $country);
			  $client->Debtor_SetCountry(array(
				'debtorHandle' => $debtor_handle,
				'value' => $country
		  ));
		} elseif ($meta_key == 'billing_address_1') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
              if($order != NULL){
                $adr1 = $order->billing_address_1;
                $adr2 = $order->billing_address_2;
                $state = $order->billing_state;
                $billing_country = $order->billing_country;
              }elseif($user_id != NULL){
                $adr1 = get_user_meta($user_id, 'billing_address_1', true);
                $adr2 = get_user_meta($user_id, 'billing_address_2', true);
                $state = get_user_meta($user_id, 'billing_state', true);
                $billing_country  = get_user_meta($user_id, 'billing_country', true);
              }else{
                $adr1 = '';
                $adr2 = '';
                $state = '';
                $billing_country = '';
              }
			  $countries = new WC_Countries();		
			  $formatted_state = (isset($state)) ? $countries->states[$billing_country][$state] : "";
			  $formatted_adr = trim($adr1."\n".$adr2."\n".$formatted_state);
			  logthis("woo_save_customer_meta_data_to_economic adr1: " . $adr1 . ", adr2: " . $adr2 . ", state: " . $formatted_state);
			  logthis("woo_save_customer_meta_data_to_economic formatted_adr: " . $formatted_adr);
			  $client->Debtor_SetAddress(array(
				'debtorHandle' => $debtor_handle,
				'value' => $formatted_adr
			  ));
	
		} elseif ($meta_key == 'billing_postcode') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $client->Debtor_SetPostalCode(array(
				'debtorHandle' => $debtor_handle,
				'value' => $meta_value
			  ));
	
		} elseif ($meta_key == 'billing_city') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $client->Debtor_SetCity(array(
				'debtorHandle' => $debtor_handle,
				'value' => $meta_value
			  ));
	
		} elseif ($meta_key == 'billing_company') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  if($order && $order->billing_company != ''){
				  $meta_value = $order->billing_company;
				  logthis('woo_save_customer_meta_data_to_economic billing_company saved as Debtor Name!');
			  }elseif($order && $order->billing_first_name != '') {
				  $meta_value = $order->billing_first_name.' '.$order->billing_last_name;
				  logthis('woo_save_customer_meta_data_to_economic billing_first_name and billing_second_name saved as Debtor Name!');
				}elseif($user_id){
				  $meta_value = get_user_meta($user_id, 'billing_company', true) ? get_user_meta($user_id, 'billing_company', true) : get_user_meta($user_id, 'billing_first_name', true).' '.get_user_meta($user_id, 'billing_last_name', true);
				  logthis('woo_save_customer_meta_data_to_economic user firstname: '.$meta_value.' is saved as Debtor Name because order is null!');
			  }else{
				  logthis('woo_save_customer_meta_data_to_economic neither order nor user, "NULL" is saved as Debtor Name!');
				  $meta_value = '';
			  }

			  $client->Debtor_SetName(array(
				'debtorHandle' => $debtor_handle,
				'value' => $meta_value
			  ));
	
		} elseif($meta_key == 'billing_first_name') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
              if($order != NULL){
                $first = $order->billing_first_name;
                $last = $order->billing_last_name;
                $email = $order->billing_email;
                $phone = $order->billing_phone;
              }elseif($user_id != NULL){
                $first = get_user_meta($user_id, 'billing_first_name', true);
                $last = get_user_meta($user_id, 'billing_last_name', true);
                $email = get_user_meta($user_id, 'billing_email', true);
                $phone = get_user_meta($user_id, 'billing_phone', true);
              }else{
                $first = '';
                $last = '';
                $email = '';
                $phone = '';
              }
			  $name = $first . " " . $last;
			  
			  $debtorAttention = $client->Debtor_GetAttention(array('debtorHandle' => $debtor_handle))->Debtor_GetAttentionResult;
			  $debtor_contact_handles = $client->Debtor_GetDebtorContacts(array('debtorHandle' => $debtor_handle))->Debtor_GetDebtorContactsResult;
			  
			  logthis('woo_save_customer_meta_data_to_economic Debtor attention:');
			  logthis($debtorAttention);
			  
			  logthis('woo_save_customer_meta_data_to_economic DebtorContactHandles:');
			  logthis($debtor_contact_handles);
			  
			  $debtorAttentionArray = (array) $debtorAttention;
			  $debtor_contact_handlesArray = (array) $debtor_contact_handles;
			  
			  if(is_array($debtor_contact_handles->DebtorContactHandle)){
				  $debtor_contact_id = $debtor_contact_handles->DebtorContactHandle[0];
			  }else{
				  $debtor_contact_id = $debtor_contact_handles->DebtorContactHandle;
			  }
			  logthis("woo_save_customer_meta_data_to_economic first contact of the debtor");
			  logthis($debtor_contact_id);
              
              logthis("woo_save_customer_meta_data_to_economic name:");
			  logthis($name);
			  
			  if(empty( $debtorAttentionArray )){
				  if(empty(  $debtor_contact_handlesArray )){
					  logthis('woo_save_customer_meta_data_to_economic: creating new debtor contact'); 
					  $debtor_contact_handle = $client->DebtorContact_Create(array(
						'debtorHandle' => $debtor_handle,
						'name' => $name))->DebtorContact_CreateResult;
					  $client->DebtorContact_SetEmail(array(
						'debtorContactHandle' => $debtor_contact_handle,
						'value' => $email));
					  $client->DebtorContact_SetTelephoneNumber(array(
						'debtorContactHandle' => $debtor_contact_handle,
						'value' => $phone));
					  logthis('woo_save_customer_meta_data_to_economic: setting new debtor contact as Debtor attention!');	
					  logthis('woo_save_customer_meta_data_to_economic debtor_contact_handle:');
					  logthis($debtor_contact_handle);			  
					  $client->Debtor_SetAttention(array(
						'debtorHandle' => $debtor_handle,
						'valueHandle' => $debtor_contact_handle
					  ));
				  }else{
					  logthis('woo_save_customer_meta_data_to_economic: setting existing debtor contact as Debtor attention!');				  
					  $client->DebtorContact_SetName(array(
						'debtorContactHandle' => $debtor_contact_id,
						'value' => $name));
					  $client->DebtorContact_SetEmail(array(
						'debtorContactHandle' => $debtor_contact_id,
						'value' => $email));
					  $client->DebtorContact_SetTelephoneNumber(array(
						'debtorContactHandle' => $debtor_contact_id,
						'value' => $phone));
					  $client->Debtor_SetAttention(array(
						'debtorHandle' => $debtor_handle,
						'valueHandle' => $debtor_contact_id
					  ));
				  }			  
			  }else{
				  logthis('woo_save_customer_meta_data_to_economic: using exiting debtor contact'); 
				  $debtor_contact_handles = $client->Debtor_GetDebtorContacts(array('debtorHandle' => $debtor_handle))->Debtor_GetDebtorContactsResult;
				  //logthis('contact name set to:'. $name);
				  $client->DebtorContact_SetName(array(
					'debtorContactHandle' => $debtor_contact_id,
					'value' => $name));
				  $client->DebtorContact_SetEmail(array(
					'debtorContactHandle' => $debtor_contact_id,
					'value' => $email));
				  $client->DebtorContact_SetTelephoneNumber(array(
					'debtorContactHandle' => $debtor_contact_id,
					'value' => $phone));
				  $client->Debtor_SetAttention(array(
					'debtorHandle' => $debtor_handle,
					'valueHandle' => $debtor_contact_id
				  ));
			  }
	
		} elseif ($meta_key == 'shipping_country') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $countries = new WC_Countries();
			  $country = $countries->countries[$meta_value];
			  logthis("woo_save_customer_meta_data_to_economic country: " . $country);
			  $client->DeliveryLocation_SetCountry (array(
				'deliveryLocationHandle' => $debtor_delivery_location_handle,
				'value' => $country
			  ));
		} elseif ($meta_key == 'shipping_postcode') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $client->DeliveryLocation_SetPostalCode(array(
				'deliveryLocationHandle' => $debtor_delivery_location_handle,
				'value' => $meta_value
			  ));
	
		} elseif ($meta_key == 'shipping_city') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
			  $client->DeliveryLocation_SetCity (array(
				'deliveryLocationHandle' => $debtor_delivery_location_handle,
				'value' => $meta_value
			  ));
	
		}
		elseif($meta_key == 'shipping_address_1') {
			  logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $meta_value);
              if($order != NULL){
                $adr1 = $order->shipping_address_1;
                $adr2 = $order->shipping_address_2;
                $state = $order->shipping_state;
                $shipping_country = $order->shipping_country;
              }elseif($user_id != NULL){
                $adr1 = get_user_meta($user_id, 'shipping_address_1', true);
                $adr2 = get_user_meta($user_id, 'shipping_address_2', true);
                $state = get_user_meta($user_id, 'shipping_state', true);
                $shipping_country = get_user_meta($user_id, 'shipping_country', true);
              }else{
                $adr1 = '';
                $adr2 = '';
                $state = '';
                $shipping_country = '';
              }
			  $countries = new WC_Countries();
			  $formatted_state = (isset($state)) ? $countries->states[$shipping_country][$state] : "";
			  $formatted_adr = trim("$adr1\n$adr2\n$formatted_state");
			  logthis("woo_save_customer_meta_data_to_economic adr1: " . $adr1 . ", adr2: " . $adr2 . ", state: " . $formatted_state);
			  logthis("debtor_delivery_location_handle:");
			  logthis($debtor_delivery_location_handle);
			  $client->DeliveryLocation_SetAddress(array(
				'deliveryLocationHandle' => $debtor_delivery_location_handle,
				'value' => $formatted_adr
			  ));
		}
		elseif($meta_key == 'billing_ean_number'){
			if($order != NULL){
				logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $order->billing_ean_number);
				if($order->billing_ean_number != '' && $order->billing_ean_number != NULL){
					$client->Debtor_SetEan(array(
						'debtorHandle' => $debtor_handle,
						'value' => $order->billing_ean_number
					));
				}
				else{
					logthis('Billing EAN number is not updated, because it is empty or NULL');
				}
			}
		}
		elseif($meta_key == 'billing_vat_number'){
			if($order != NULL){
				logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $order->billing_vat_number);
				if($order->billing_vat_number != '' && $order->billing_vat_number != NULL){
					$client->Debtor_SetVatNumber(array(
						'debtorHandle' => $debtor_handle,
						'value' => $order->billing_vat_number
					));
				}
				else{
					logthis('Billing VAT number is not updated, because it is empty or NULL');
				}
			}
		}elseif($meta_key == 'billing_cin_number'){
			if($order != NULL){
				logthis("woo_save_customer_meta_data_to_economic key: " . $meta_key . " value: " . $order->billing_cin_number);
				if($order->billing_cin_number != '' && $order->billing_cin_number != NULL){
					$client->Debtor_SetCINumber(array(
						'debtorHandle' => $debtor_handle,
						'value' => $order->billing_cin_number
					));
				}
				else{
					logthis('Billing CIN number is not updated, because it is empty or NULL');
				}
			}
		}else{
			logthis("woo_save_customer_meta_data_to_economic unknown meta_key :".$meta_key." meta_value: ".$meta_value);
		}
		return true;
	  } catch (Exception $exception) {
		logthis("woo_save_customer_meta_data_to_economic could not update debtor: " . $exception->getMessage());
		$this->debug_client($client);
		logthis("Could not update debtor.");
		logthis($exception->getMessage());
		return false;
	  }
	}
	
	/**
     * Sync WooCommerce users to e-conomic 
     *
     * @access public
     * @param 
     * @return array log
     */
	public function sync_contacts(){
		global $wpdb;
		$client = $this->woo_economic_client();
		$sync_log = array();
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'msg' => __('Could not create e-conomic client, please try again later!', 'woocommerce-e-conomic-integration') ));
			return $sync_log;
		}
		$users = array();
		$orders = array();
		$sync_log[0] = true;
		logthis("sync_contacts starting...");
		$args = array(
			'role' => 'customer',
		);
		$customers = get_users( $args );
		foreach ($customers as $customer){
			if($customer->get('debtor_number') == ''){
				array_push($users, $customer);
			}
		}
        $unsynced_users = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE synced = 0 AND user_id != 0");
		foreach ($unsynced_users as $user){
			array_push($users, new WP_User($user->user_id));
		}
		
		$unsynced_guest_users = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE synced = 0 AND user_id = 0");
		foreach ($unsynced_guest_users as $guest_user){
			$unsynced_guest_user_orders = $wpdb->get_results("SELECT * FROM  ".$wpdb->prefix."postmeta WHERE  meta_value = '".$guest_user->email."' ORDER BY post_id DESC");
			foreach ($unsynced_guest_user_orders as $order){
				array_push($orders, new WC_Order($order->post_id));
				break;
			}
		}
		
		//logthis($users);
		if(!empty($users)){
			foreach ($users as $user) {
				logthis('sync_contacts User ID: ' . $user->ID);
				if($this->save_customer_to_economic($client, $user)){
					array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => $user->ID, 'msg' => __('Customer synced successfully', 'woocommerce-e-conomic-integration') ));
				}else{
					$sync_log[0] = false;
					array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'user_id' => $user->ID, 'msg' => __('Sync failed, please try again later!', 'woocommerce-e-conomic-integration') ));
				}
			}
		}
		if(!empty($orders)){
			foreach ($orders as $order) {
				logthis('sync_contacts User email (guest user): ' . $order->billing_email);
				if($this->save_customer_to_economic($client, NULL, $order)){
					array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => $order->billing_email, 'msg' => __('Guest customer synced successfully', 'woocommerce-e-conomic-integration') ));
				}else{
					$sync_log[0] = false;
					array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'user_id' => $order->billing_email, 'msg' => __('Guest customer sync failed, please try again later!', 'woocommerce-e-conomic-integration') ));
				}
			}
		}
		
		if(empty($users) && empty($orders)){
			$sync_log[0] = true;
			array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => '', 'msg' => __('All customers were already synced!', 'woocommerce-e-conomic-integration') ));
		}

		$client->Disconnect();
		logthis("sync_contacts ending...");
		return $sync_log;
	}
	
	
	/**
     * Sync e-conomice users to  WooCommerce
     *
     * @access public
     * @param 
     * @return array log
     */
	 
	public function sync_contacts_ew(){
		update_option('woo_save_object_to_economic', false);
		logthis('sync_contacts_ew: syncing e-conomic customer sto WC store starts...');
		global $wpdb;
		$client = $this->woo_economic_client();
		$sync_log = array();
		$sync_log[0] = true;
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'msg' => __('Could not create e-conomic client, please try again later!', 'woocommerce-e-conomic-integration') ));
			return $sync_log;
		}
		
		$debtors = $client->Debtor_GetAll()->Debtor_GetAllResult;
		//logthis($debtors);
		
		$debtor_handles = array();
		
		foreach($debtors->DebtorHandle as $debtor){
			$debtor_handles[$debtor->Number] = $client->Debtor_GetDebtorGroup(array('debtorHandle' => $debtor))->Debtor_GetDebtorGroupResult;
		}
		//logthis('debtor_handle_list');
		foreach($debtor_handles as $debtor_number => $group){
			logthis('sync_contacts_ew: customer group number: '.$group->Number);
			if($group->Number == $this->customer_group){
				logthis('sync_contacts_ew: debtor_number:'.$debtor_number.' is being synced.');
				$debtor_email = $client->Debtor_GetEmail(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetEmailResult;
				logthis('sync_contacts_ew: email: '.$debtor_email);
				
				$debtor_contact_handles = $client->Debtor_GetDebtorContacts(array('debtorHandle' => array('Number' => $debtor_number )))->Debtor_GetDebtorContactsResult;
				//logthis('debtor_contact_handles:');
				//logthis($debtor_contact_handles);
				
				if(is_array($debtor_contact_handles->DebtorContactHandle)){
					$debtor_contact_handle = $debtor_contact_handles->DebtorContactHandle[0];
				}else{
					$debtor_contact_handle = $debtor_contact_handles->DebtorContactHandle;
				}
				
				if(!empty($debtor_contact_handle) && $debtor_contact_handle!=''){
					$debtor_name = $client->DebtorContact_GetName(array(
						'debtorContactHandle' => $debtor_contact_handle
					))->DebtorContact_GetNameResult;
					logthis('sync_contacts_ew: name from debtor\'s first contact:');
					logthis($debtor_name);
					$debtor_name = explode(' ', $debtor_name );
					logthis($debtor_name);
				}else{
					$debtor_name = $client->Debtor_GetName(array(
						'debtorHandle' => array('Number' => $debtor_number ),
					))->Debtor_GetNameResult;
					logthis('sync_contacts_ew: name from debtors name:');
					logthis($debtor_name);
					$debtor_name = explode(' ', $debtor_name);
					logthis($debtor_name);
				}
				
				$debtor_phone = $client->Debtor_GetTelephoneAndFaxNumber(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetTelephoneAndFaxNumberResult;
				logthis('sync_contacts_ew: phone:'.$debtor_phone);
				
				$debtor_country = $client->Debtor_GetCountry(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetCountryResult;
				logthis('sync_contacts_ew: country:'.$debtor_country);
				
				$debtor_country_code = array_search($debtor_country, $this->countrycodes);
				logthis('sync_contacts_ew: debtor_country_code:'.$debtor_country_code);
				
				$debtor_address = $client->Debtor_GetAddress(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetAddressResult;
				logthis('sync_contacts_ew: debtor_address:'.$debtor_address);
				
				$debtor_PostalCode = $client->Debtor_GetPostalCode(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetPostalCodeResult;
				logthis('sync_contacts_ew: debtor_PostalCode:'.$debtor_PostalCode);
				
				$debtor_city = $client->Debtor_GetCity(array(
					'debtorHandle' => array('Number' => $debtor_number ),
				))->Debtor_GetCityResult;
				logthis('sync_contacts_ew: debtor_city:'.$debtor_city);
				
				//logthis($debtor_email);
				//logthis($debtor_name);
				
				$user = get_user_by( 'email', $debtor_email );
								
				if($wpdb->query('SELECT user_id FROM '.$wpdb->prefix.'usermeta WHERE meta_key = "debtor_number" AND meta_value = '.$debtor_number)){
					logthis('update customer meta: '.$debtor_number);
					$userdata = array(
						'ID' => $user->ID,
						//'user_login' => strtolower($debtor_name[0]),
						'first_name' => $debtor_name[0],
						'last_name' => $debtor_name[1],
						'user_email' => $debtor_email,
						//'role' => 'customer'
					);
					
					$customer = wp_update_user( $userdata );
					//logthis($customer);
					if ( ! is_wp_error( $customer ) ) {
						logthis("User updated : ". $customer ." for debtor_number: ". $debtor_number);
						$sync_log[0] = true;
						update_user_meta($customer, 'debtor_number', $debtor_number);
						update_user_meta($customer, 'billing_first_name', $debtor_name[0]);
						update_user_meta($customer, 'billing_last_name', $debtor_name[1]);
						update_user_meta($customer, 'billing_phone', $debtor_phone);
						update_user_meta($customer, 'billing_country', $debtor_country_code);
						update_user_meta($customer, 'billing_address_1', $debtor_address);
						update_user_meta($customer, 'billing_postcode', $debtor_PostalCode);
						update_user_meta($customer, 'billing_city', $debtor_city);
						array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => $customer, 'msg' => __('User '.$debtor_name[0].' '.$debtor_name[1].' with customer role updated!', 'woocommerce-e-conomic-integration') ));	
					}else{
						logthis($customer);
					}
				}else{					
					if($user){
						logthis('update customer: '.$debtor_number);
						$userdata = array(
							'ID' => $user->ID,
							//'user_login' => strtolower($debtor_name[0]),
							'first_name' => $debtor_name[0],
							'last_name' => isset($debtor_name[1])? $debtor_name[1] : '',
							'user_email' => $debtor_email,
							//'role' => 'customer'
						);
						
						$customer = wp_update_user( $userdata );
						//logthis($customer);
						if ( ! is_wp_error( $customer ) ) {
							logthis("User updated : ". $customer ." for debtor_number: ". $debtor_number);
							$sync_log[0] = true;
							update_user_meta($customer, 'debtor_number', $debtor_number);
							update_user_meta($customer, 'billing_first_name', $debtor_name[0]);
							update_user_meta($customer, 'billing_last_name', $debtor_name[1]);
							update_user_meta($customer, 'billing_phone', $debtor_phone);
							update_user_meta($customer, 'billing_country', $debtor_country_code);
							update_user_meta($customer, 'billing_address_1', $debtor_address);
							update_user_meta($customer, 'billing_postcode', $debtor_PostalCode);
							update_user_meta($customer, 'billing_city', $debtor_city);
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => $customer, 'msg' => __('User '.$debtor_name[0].' with customer role updated!', 'woocommerce-e-conomic-integration') ));	
						}else{
							logthis($customer);
						}
					}else{
						logthis('add new customer: '.$debtor_number);
						$userdata = array(
							'user_login' => strtolower($debtor_name[0].'_'.$debtor_number),
							'first_name' => $debtor_name[0],
							'last_name' => $debtor_name[1],
							'user_email' => $debtor_email,
							'role' => 'customer'
						);
						
						$customer = wp_insert_user( $userdata );
						//logthis($customer);
						if ( ! is_wp_error( $customer ) ) {
							logthis("User created : ". $customer ." for debtor_number: ". $debtor_number);
							$sync_log[0] = true;
							update_user_meta($customer, 'debtor_number', $debtor_number);
							update_user_meta($customer, 'billing_first_name', $debtor_name[0]);
							update_user_meta($customer, 'billing_last_name', $debtor_name[1]);
							update_user_meta($customer, 'billing_phone', $debtor_phone);
							update_user_meta($customer, 'billing_country', $debtor_country_code);
							update_user_meta($customer, 'billing_address_1', $debtor_address);
							update_user_meta($customer, 'billing_postcode', $debtor_PostalCode);
							update_user_meta($customer, 'billing_city', $debtor_city);
							array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'user_id' => $customer, 'msg' => __('New user '.$debtor_name[0].' '.$debtor_name[1].' with customer role created!', 'woocommerce-e-conomic-integration') ));	
							if(!$wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email='".$debtor_email."'")){
								$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => $customer, 'customer_number' => $debtor_number, 'email' => $debtor_email, 'synced' => 1), array('%d', '%s', '%s', '%d'));
							}
						}else{
							if(!$wpdb->query ("SELECT * FROM ".$wpdb->prefix."wce_customers WHERE email='".$debtor_email."'")){
								$wpdb->insert ($wpdb->prefix."wce_customers", array('user_id' => 0, 'customer_number' => $debtor_number, 'email' => $debtor_email, 'synced' => 0), array('%d', '%s', '%s', '%d'));
							}
						}
					}								
				}
			}else{
				//$sync_log[0] = false;
				//array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'user_id' => NULL, 'msg' => __('Customer group doesn\'t match the Customer group in settings!', 'woocommerce-e-conomic-integration') ));
				logthis("Customer group doesn't match the Customer group in settings: ".$customer);
			}
		}
		update_option('woo_save_object_to_economic', true);
		logthis('sync_contacts_ew: syncing e-conomic customer sto WC store ends...');
		return $sync_log;
	}
	
	
	/**
     * Save WooCommerce Shipping to e-conomic
     *
     * @access public
     * @param shipping settings array
     * @return bool
     */
	
	public function save_shipping_to_economic($shippingMethodObject, SoapClient &$client){
		if(!$client){
			return false;
		}
		
		logthis("save_shipping_to_economic syncing shipping ID - sku: " . $shippingMethodObject->id . " title: " . $shippingMethodObject->title);
		try	{
			//logthis($shippingMethodObject);
			$shippingID = $shippingMethodObject->id;
			
			//Added to eleminate shipping ID/e-conomnic product ID length more than 25. This same check should be added when shipping method is added as orderline or invoice line.
			if(strlen($shippingID) > 25){
				$shippingID = substr($shippingID, 0, 24);
			}
			//$shippingTitle = $shippingMethodObject->title;
			logthis("save_shipping_to_economic - trying to find shipping in economic");
			
			// Find product by number
			$product_handle = $client->Product_FindByNumber(array(
			'number' => $shippingID))->Product_FindByNumberResult;
			
			// Create product with name
			if (!$product_handle) {
				$productGroupHandle = $client->ProductGroup_FindByNumber(array(
				'number' => $this->shipping_group))->ProductGroup_FindByNumberResult;
				$product_handle = $client->Product_Create(array(
				'number' => $shippingID,
				'productGroupHandle' => $productGroupHandle,
				'name' => $shippingMethodObject->title))->Product_CreateResult;
				logthis("save_shipping_to_economic - shipping created:" . $shippingMethodObject->title);
			}else{
				$client->Product_SetProductGroup(array(
					'productHandle' => $product_handle,
					'valueHandle' => array('Number' => $this->shipping_group)
				 ));
			}
			
			// Get product data
			$product_data = $client->Product_GetData(array(
			'entityHandle' => $product_handle))->Product_GetDataResult;
			
			if(isset($shippingMethodObject->settings['additional_costs']) && $shippingMethodObject->settings['additional_costs'] > 0){
				$shippingCost = $shippingMethodObject->cost + $shippingMethodObject->cost + $shippingMethodObject->settings['additional_costs'];
				if($shippingMethodObject->fee >= $shippingMethodObject->minimum_fee){
					$shippingCost = $shippingCost + $shippingMethodObject->fee;
				}else{
					$shippingCost = $shippingCost + $shippingMethodObject->minimum_fee;
				}
			}else{
				$shippingCost = $shippingMethodObject->cost;
				if($shippingMethodObject->fee >= $shippingMethodObject->minimum_fee){
					$shippingCost = $shippingCost + $shippingMethodObject->fee;
				}else{
					$shippingCost = $shippingCost + $shippingMethodObject->minimum_fee;
				}
			}
			
			
			
			$Company = $client->Company_Get()->Company_GetResult;
			$Company_GetBaseCurrency = $client->Company_GetBaseCurrency(array(
				'companyHandle' => $Company
			))->Company_GetBaseCurrencyResult;
			logthis('Company_GetBaseCurrency:');
			logthis($Company_GetBaseCurrency);
			
			if($Company_GetBaseCurrency->Code == get_option('woocommerce_currency')){
				$shippingCost1 = $shippingCost;
			}else{
				$shippingCost1 = $client->Product_GetSalesPrice(array('productHandle'  => $product_handle))->Product_GetSalesPriceResult;
			}
			
			// Update product data
			$client->Product_UpdateFromData(array(
			'data' => (object)array(
			'Handle' => $product_data->Handle,
			'Number' => $product_data->Number,
			'ProductGroupHandle' => $product_data->ProductGroupHandle,
			'Name' => $shippingMethodObject->title,
			'Description' => $shippingMethodObject->title,
			'BarCode' => "",
			'SalesPrice' => $shippingCost1 > 0 ? $shippingCost1 : 0.0,
			'CostPrice' => (isset($product_data->CostPrice) ? $product_data->CostPrice : 0.0),
			'RecommendedPrice' => $product_data->RecommendedPrice,
			/*'UnitHandle' => (object)array(
			'Number' => 1
			),*/
			'IsAccessible' => true,
			'Volume' => $product_data->Volume,
			//'DepartmentHandle' => $product_data->DepartmentHandle,
			//'DistributionKeyHandle' => $product_data->DistrubutionKeyHandle,
			'InStock' => $product_data->InStock,
			'OnOrder' => $product_data->OnOrder,
			'Ordered' => $product_data->Ordered,
			'Available' => $product_data->Available)))->Product_UpdateFromDataResult;
			
			//Added in version 1.9.9.9.1 by Alvin for updaing the product price in store currency settings.
			if($Company_GetBaseCurrency->Code != get_option('woocommerce_currency')){
				$productPriceHandle = $client->ProductPrice_FindByProductAndCurrency(array(
					'productHandle'  => $product_handle,
					'currencyHandle' => array('Code' => get_option('woocommerce_currency')),
				))->ProductPrice_FindByProductAndCurrencyResult;
				
				if(isset($productPriceHandle) && !empty($productPriceHandle)){
					logthis('productPriceHandle:');
					logthis($productPriceHandle);
					$client->ProductPrice_SetPrice(array(
						'productPriceHandle'  => $productPriceHandle,
						'value'			 	  => $shippingCost > 0 ? $shippingCost : 0.0
					));
				}else{
					logthis('productPriceHandle not found, creating product price.');
					$client->ProductPrice_Create(array(
						'productHandle'  => $product_handle,
						'currencyHandle' => array('Code' => get_option('woocommerce_currency')),
						'price'			 => $shippingCost > 0 ? $shippingCost : 0.0
					));
				}
			}
			
			logthis("save_shipping_to_economic - product updated : " . $shippingMethodObject->title);
			return true;
		} catch (Exception $exception) {
			logthis("save_shipping_to_economic could not create product: " . $exception->getMessage());
			$this->debug_client($client);
			logthis($exception->getMessage);
			return false;
		}
	}
	
	
	/**
     * Sync WooCommerce shipping as products to e-conomic 
     *
     * @access public
     * @param 
     * @return array log
     */
	public function sync_shippings(){
		$client = $this->woo_economic_client();
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'msg' => __('Could not create e-conomic client, please try again later!', 'woocommerce-e-conomic-integration') ));
			return $sync_log;
		}
		$sync_log = array();
		$sync_log[0] = true;
		$shipping = new WC_Shipping();
		$shippingMethods = $shipping->load_shipping_methods();
		
		//Added for WC_Shipping_Table_Rate support 1.9.9.3
		/*$WC_Shipping_Table_Rate = new WC_Shipping_Table_Rate();
		$shippingMethods['table_rate'] = $WC_Shipping_Table_Rate;*/
		//Added for WC_Shipping_Table_Rate support 1.9.9.3
		
		/*Added for WooConomics 1.9.17*/
		if (class_exists('WC_Shipping_Zones')) {
			$shippingZones = new WC_Shipping_Zones();
			foreach ($shippingZones->get_zones() as $shippingZone){
				$zone = $shippingZone['zone_name'];
				foreach($shippingZone['shipping_methods'] as $zoneShippingMethods){
					$zoneShippingMethods->id = $zoneShippingMethods->id.':'.$zoneShippingMethods->instance_id;
					$zoneShippingMethods->title = $zone.' '.$zoneShippingMethods->title;
					$shippingMethods[$zoneShippingMethods->id] = $zoneShippingMethods;
				}
			}
		}
		/*Added for WooConomics 1.9.17*/
		
		logthis("sync_shippings starting...");
		//logthis($shippingMethods);
		foreach ($shippingMethods as $shippingMethod => $shippingMethodObject) {
			logthis('Shipping ID: '. $shippingMethodObject->id );
			logthis('Shipping Title: '. $shippingMethodObject->title);
			if($shippingMethodObject->title != '' && isset($shippingMethodObject->title)){
				$title = $shippingMethodObject->title;
			}else{
				$shippingMethodObject->title = $shippingMethodObject->id;
			}
			if($this->save_shipping_to_economic($shippingMethodObject, $client)){
				array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $shippingMethodObject->id, 'name' => $shippingMethodObject->title, 'msg' => __('Shipping synced successfully', 'woocommerce-e-conomic-integration') ));
			}else{
				$sync_log[0] = false;
				array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'sku' => $shippingMethodObject->id, 'name' => $shippingMethodObject->title, 'msg' => __('Shipping not synced, please try again!', 'woocommerce-e-conomic-integration') ));
			}
		}
		
		$client->Disconnect();
		logthis("sync_shippings ending...");
		return $sync_log;
	}
	
	
	/**
     * Save WooCommerce Coupon to e-conomic
     *
     * @access public
     * @param coupon object
     * @return bool
     */
	
	public function save_coupon_to_economic($coupon, SoapClient &$client){
		if(!$client){
			return false;
		}
		$couponID = $coupon->post_title;
			
		//Added to eleminate shipping ID/e-conomnic product ID length more than 25. This same check should be added when shipping method is added as orderline or invoice line.
		if(strlen($couponID) > 25){
			$couponID = substr($couponID, 0, 24);
		}
	
		logthis("save_coupon_to_economic syncing shipping ID: " . $coupon->ID . " title: " . $coupon->post_title);
		try	{			
			logthis("save_coupon_to_economic - trying to find shipping in economic");
			
			// Find product by number
			$product_handle = $client->Product_FindByNumber(array(
			'number' => $couponID))->Product_FindByNumberResult;
			
			// Create product with name
			if (!$product_handle) {
				$productGroupHandle = $client->ProductGroup_FindByNumber(array(
				'number' => $this->coupon_group))->ProductGroup_FindByNumberResult;
				$product_handle = $client->Product_Create(array(
				'number' => $couponID,
				'productGroupHandle' => $productGroupHandle,
				'name' => $coupon->post_title))->Product_CreateResult;
				logthis("save_coupon_to_economic - coupon created:" . $coupon->post_title);
			}else{
				$client->Product_SetProductGroup(array(
					'productHandle' => $product_handle,
					'valueHandle' => array('Number' => $this->coupon_group)
				 ));
			}
			
			// Get product data
			$product_data = $client->Product_GetData(array(
			'entityHandle' => $product_handle))->Product_GetDataResult;
			
			$couponCost = 0.0;
			
			
			// Update product data
			$client->Product_UpdateFromData(array(
			'data' => (object)array(
			'Handle' => $product_data->Handle,
			'Number' => $product_data->Number,
			'ProductGroupHandle' => $product_data->ProductGroupHandle,
			'Name' => $coupon->post_title,
			'Description' => $coupon->post_excerpt,
			'BarCode' => "",
			'SalesPrice' => $couponCost,
			'CostPrice' => (isset($product_data->CostPrice) ? $product_data->CostPrice : 0.0),
			'RecommendedPrice' => $product_data->RecommendedPrice,
			/*'UnitHandle' => (object)array(
			'Number' => 1
			),*/
			'IsAccessible' => true,
			'Volume' => $product_data->Volume,
			//'DepartmentHandle' => $product_data->DepartmentHandle,
			//'DistributionKeyHandle' => $product_data->DistrubutionKeyHandle,
			'InStock' => $product_data->InStock,
			'OnOrder' => $product_data->OnOrder,
			'Ordered' => $product_data->Ordered,
			'Available' => $product_data->Available)))->Product_UpdateFromDataResult;
			
			logthis("save_coupon_to_economic - product updated : " . $coupon->post_title);
			return true;
		} catch (Exception $exception) {
			logthis("save_coupon_to_economic could not create coupon: " . $exception->getMessage());
			$this->debug_client($client);
			logthis($exception->getMessage);
			return false;
		}
	}
	
	
	
	/**
     * Sync WooCommerce Coupons as products to e-conomic Added in version 1.9.9.4
     *
     * @access public
     * @param 
     * @return array log
     */
	public function sync_coupons(){
		$client = $this->woo_economic_client();
		if(!$client){
			$sync_log[0] = false;
			array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'msg' => __('Could not create e-conomic client, please try again later!', 'woocommerce-e-conomic-integration') ));
			return $sync_log;
		}
		$sync_log = array();
		$sync_log[0] = true;
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_type'        => 'shop_coupon',
			'post_status'      => 'publish',
		);	
		$coupons = get_posts( $args );
		
		foreach ($coupons as $coupon) {
			//logthis($coupon);
			//logthis('Coupon ID: '. $coupon->ID );
			logthis('Coupon Title: '. $coupon->post_title);
			$title = $coupon->post_title;
			if($this->save_coupon_to_economic($coupon, $client)){
				array_push($sync_log, array('status' => __('success', 'woocommerce-e-conomic-integration'), 'sku' => $coupon->ID, 'name' => $coupon->post_title, 'msg' => __('Coupon synced successfully', 'woocommerce-e-conomic-integration') ));
			}else{
				$sync_log[0] = false;
				array_push($sync_log, array('status' => __('fail', 'woocommerce-e-conomic-integration'), 'sku' => $coupon->ID, 'name' => $coupon->post_title, 'msg' => __('Coupon not synced, please try again!', 'woocommerce-e-conomic-integration') ));
			}
		}
		
		$client->Disconnect();
		logthis("sync_coupons ending...");
		return $sync_log;
	}
	
	
	/**
     * Send inovice of an order from e-conomic to customers
     *
     * @access public
     * @param user object, order object, e-conomic client
     * @return boolean
     */
	public function send_invoice_economic(SoapClient &$client, WC_Order $order = NULL){
		try{
			if(isset($order->order_number) && $order->order_number != NULL){
				$orderId = $order->order_number;
			}else{
				$orderId = $order->id;
			}
			$current_invoice_handle = $client->CurrentInvoice_FindByOtherReference(array(
				'otherReference' => $this->order_reference_prefix.$orderId
			))->CurrentInvoice_FindByOtherReferenceResult;
			
			logthis('send_invoice_economic CurrentInvoiceHandleId:'. $current_invoice_handle->CurrentInvoiceHandle->Id);
			logthis($current_invoice_handle);
			
			logthis('send_invoice_economic book invoice');
			
			$invoice = $client->CurrentInvoice_Book(array(
				'currentInvoiceHandle' => $current_invoice_handle->CurrentInvoiceHandle
			))->CurrentInvoice_BookResult;
			
			logthis('send_invoice_economic invoice: '. $invoice->Number);
			logthis($invoice);
			
			$pdf_invoice = $client->Invoice_GetPdf(array(
				'invoiceHandle' => $invoice
			))->Invoice_GetPdfResult;
			
			//logthis('send_invoice_economic pdf_base64_data:');
			//logthis($pdf_invoice);
			
			logthis('send_invoice_economic Creating PDF invoice');
			$filename = 'ord_'.$order->id.'-inv_'.$invoice->Number.'.pdf';
			$path = dirname(__FILE__).'/invoices/';
			$file = $path.$filename;
			if(!file_exists($file)){
				$fileobject = fopen($file, 'w');
			}
			fwrite ($fileobject, $pdf_invoice);
			fclose ($fileobject);
			logthis('send_invoice_economic Invoice '.$file.' is created');
			
			$to = $order->billing_email;
			$orderDate = explode(' ', $order->order_date);
			$subject = get_bloginfo( $name ).' - Invoice no. '.$invoice->Number.' - '.$orderDate[0];
			$body = '';
			/*$random_hash = md5(date('r', time())); 
			//$headers = 'Content-Type: text/html; charset=UTF-8';
			//$headers. = 'From: '.get_bloginfo( 'name' ).' <'.get_bloginfo( 'admin_email' ).'>';
			
			$headers = "MIME-Version: 1.0" . "\r\n";
			//$headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
			$headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-".$random_hash."\""; 
			$headers .= "From: ".get_bloginfo( 'name' )." <"..">"."\r\n";
		
			//logthis('To: '.$to.'/n Subject: '.$subject.'/n Headers: '.$headers);*/
			if($order->payment_method == 'economic-invoice'){
				logthis('send_invoice_economic calling mail_attachment');
				return $this->mail_attachment($filename, $path, $to, get_bloginfo( 'admin_email' ), get_bloginfo( 'name' ), get_bloginfo( 'admin_email' ), $subject, $body );
			}
			return true;
		}catch (Exception $exception) {
			logthis($exception->getMessage);
			$this->debug_client($client);
			return false;
		}
	}
	
	public function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message) {
		$file = $path.$filename;
		$file_size = filesize($file);
		//logthis('file_size: '.$file_size);
		$handle = fopen($file, "r");
		$content = fread($handle, $file_size);
		//logthis('content: '.$content);
		fclose($handle);
		$content = chunk_split(base64_encode($content));
		$uid = md5(uniqid(time()));
		$name = basename($file);
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
		$header .= "Reply-To: ".$replyto."\r\n";
		$header .= "MIME-Version: 1.0\r\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= "--".$uid."\r\n";
		$header .= "Content-type:text/plain; charset=iso-8859-1\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
		$header .= $message."\r\n\r\n";
		$header .= "--".$uid."\r\n";
		$header .= "Content-Type: application/octet-stream; name=\"".$filename."\"\r\n"; // use different content types here
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n\r\n";
		$header .= $content."\r\n\r\n";
		$header .= "--".$uid."--";
		logthis('mail_attachment sending mail');
		return mail($mailto, $subject, "", $header);
	}

}