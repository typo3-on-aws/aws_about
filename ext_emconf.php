<?php

/*
 * This file is part of the TYPO3 CMS Extension "AWS About"
 * Extension author: Michael Schams - https://schams.net
 *
 * For copyright and license information, please read the README.md
 * file distributed with this source code.
 *
 * @package     TYPO3
 * @subpackage  aws_about
 * @author      Michael Schams <schams.net>
 * @link        https://typo3-on-aws.org
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'TYPO3-on-AWS Backend Start Screen',
    'description' => 'Backend welcome screen for TYPO3-on-AWS instances',
    'category' => 'module',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Michael Schams <schams.net>',
    'author_email' => '',
    'author_company' => 'schams.net',
    'version' => '10.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.0.0-10.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
