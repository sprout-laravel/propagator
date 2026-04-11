<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Config Management Mode
    |--------------------------------------------------------------------------
    |
    | This value determines how Propagator applies config changes.
    |
    | "managed" — Propagator reads the existing config file, applies changes,
    | and writes the whole file back. The output is functional but
    | machine-generated (no comments, basic formatting).
    |
    | "manual" — Propagator outputs a PHP snippet to the console for you
    | to paste into your config file yourself.
    |
    | Supported: "managed", "manual"
    |
    */

    'mode' => 'managed',

];
