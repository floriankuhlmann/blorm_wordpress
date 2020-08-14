<?php


/**
 * Class BlormUser
 */
class BlormUser {

    /**
     * Hold the class instance.
     * @var null
     */
    private static $instance = null;

    /**
     * the object with the user data (if everything goes right)
     * @var stdClass
     */
    private static $returnObj;

    /**
     * The constructor is private
     * to prevent initiation with outer code.
     * use function getInstance() instead
     * BlormUser constructor.
     */
    private function __construct()
    {

        // init userdateObject
        self::$returnObj = $this->getUserDataFromBlorm();
    }

    /**
     * The object is created from within the class itself
     * only if the class has no instance.
     * @return BlormUser|null
     */
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new BlormUser();
        }

        return self::$instance;
    }

    /**
     * @return stdClass
     */
    private function getUserDataFromBlorm() {

        $returnObj = new stdClass();
        $returnObj->error = null;
        $returnObj->user = null;

        // prepare the request
        $args = array(
            'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
            'method' => 'GET',
            'body' => '',
            'data_format' => 'body',
        );

        // @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
        $ApiResponse = wp_remote_request(CONFIG_BLORM_APIURL ."/user/data", $args);

        if (is_a($ApiResponse,"WP_ERROR")) {

            if (isset($ApiResponse->errors['http_request_failed'])) {
                error_log($ApiResponse->errors['http_request_failed'][0]);
            }

            $returnObj->error = $ApiResponse;
            return $returnObj;
        }

        $jsonObjResponse = json_decode($ApiResponse["body"]);

        $user = new stdClass();
        $user->name = $jsonObjResponse->name;
        $user->blormhandle = $jsonObjResponse->blormhandle;
        $user->id = $jsonObjResponse->id;
        $user->website = $jsonObjResponse->website;
        $user->photo_url = $jsonObjResponse->photo_url;
        $returnObj->user = $user;

        return $returnObj;

    }

    /**
     * @return stdClass
     */
    public function getUserData() {

        return self::$returnObj;

    }

}


function getUserDataFromBlorm() {

    $returnObj = new stdClass();
    $returnObj->error = null;
    $returnObj->user = null;

    // prepare the request
    $args = array(
        'headers' => array('Authorization' => 'Bearer '.get_blorm_config_param('api_key'), 'Content-type' => 'application/json'),
        'method' => 'GET',
        'body' => '',
        'data_format' => 'body',
    );

    // @return array|WP_Error Array containing 'headers', 'body', 'response', 'cookies', 'filename'.
    $ApiResponse = wp_remote_request(CONFIG_BLORM_APIURL ."/user/data", $args);

    if (is_a($ApiResponse,"WP_ERROR")) {

        if (isset($ApiResponse->errors['http_request_failed'])) {
            error_log($ApiResponse->errors['http_request_failed'][0]);
        }

        $returnObj->error = $ApiResponse;
        return $returnObj;
    }

    $jsonObjResponse = json_decode($ApiResponse["body"]);

    $user = new stdClass();
    $user->name = $jsonObjResponse->name;
    $user->blormhandle = $jsonObjResponse->blormhandle;
    $user->id = $jsonObjResponse->id;
    $user->website = $jsonObjResponse->website;
    $user->photo_url = $jsonObjResponse->photo_url;
    $returnObj->user = $user;

    return $returnObj;

}