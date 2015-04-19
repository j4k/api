<?php

return [

    /*
     * Strict Mode
     * Throws an exception if the correct accept header is not found
     * Used in the main API Middleware
     */
    'strict' => true,

    /*
     * Default Version of the API to serve if one isn't specified
     * in the accept header of the request
     */
    'default_version' => ['v1.0', 'json'],

    /*
     * Extensions that this API supports
     * Main API Middleware will throw an exception if the request asks for anything not supported
     * by this API.
     */
    'extensions' => ['bulk', 'jsonpatch']

];
