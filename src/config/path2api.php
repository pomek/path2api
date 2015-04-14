<?php

return [

    /**
     * Prefix URL.
     */
    'prefix' => 'api',


    /**
     * File name of the generated documentation.
     */
    'file' => 'api.md',


    /**
     * Text will be append before docs.
     */
    'before' => join("\n", [
        '# API Documentation' . "\n",
        'Documentation generates by **Path2API** package.' . "\n",
        '---' . "\n",
    ]),


    /**
     * Text will be append after docs.
     */
    'after' => join("\n", [
        '---' . "\n",
        'Generates by [Path2API](//github.com/pomek/path2api)',
    ]),


    /**
     * Template for a single route.
     */
    'template' => function ($uri, $description, array $methods, array $params, array $throws) {
        // To upper each HTTP method
        $methods = array_map(function ($item) {
            return strtoupper($item);
        }, $methods);

        // Remove HTTP "HEAD" if exist
        $methods = array_filter($methods, function ($item) {
            return $item !== "HEAD";
        });

        $response = [
            sprintf('### `%s`: %s' . "\n", join('|', $methods), $uri),
        ];

        if (null !== $description) {
            $response[] = $description;
        }

        if (0 !== count($params)) {
            $response[] = "\n" . '**Params:**';

            foreach ($params as $param_name => $param_types) {
                $response[] = sprintf(' * `%s` `%s`', join(',', $param_types), $param_name);
            }
        }

        if (0 !== count($throws)) {
            $response[] = "\n" . '**Throws:**';

            $response[] = join("\n", array_map(function ($item) {
                return sprintf(' * `%s`', $item);
            }, $throws));
        }

        $response[] = "\n";

        return join("\n", $response);
    },

];
