<?php

return [

    /**
     * Prefix URL.
     */
    'prefix' => 'api',


    /**
     * File name of the generated documentation.
     */
    'filename' => 'api.md',


    /**
     * Text will be append before docs.
     */
    'before' => '# API Documentation',


    /**
     * Text will be append after docs.
     */
    'after' => 'Generates by [Path2API](//github.com/pomek/path2api)',


    /**
     * Template for a single route.
     */
    'template' => function ($uri, $description, array $throws, array $params) {
        $response = [
            sprintf('### %s', $uri),
        ];

        if (null !== $description) {
            $response[] = $description;
            $response[] = '';
        }

        if (0 !== count($throws)) {
            $response[] = '**Throws:**';

            $response[] = join("\n", array_map(function ($item) {
                return sprintf(' * `%s`', $item);
            }, $throws));

            $response[] = '';
        }

        if (0 !== count($params)) {
            $response[] = '**Params:**';

            foreach ($params as $param_name => $param_types) {
                $response[] = sprintf(' * `%s` `%s`', $param_name, join(',', $param_types));
            }

            $response[] = '';
        }

        $response[] = '---';

        return join("\n", $response);
    },

];
