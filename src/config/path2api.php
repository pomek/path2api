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
    'template' => function ($uri, $description, array $params, array $throws) {
        $response = [
            sprintf('### URL: %s' . "\n", $uri),
        ];

        if (null !== $description) {
            $response[] = $description;
        }

        if (0 !== count($params)) {
            $response[] = "\n" . '**Params:**';

            foreach ($params as $param_name => $param_types) {
                $response[] = sprintf(' * `%s` `%s`', $param_name, join(',', $param_types));
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
