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

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addModule(
    'help',
    'AwsAbout',
    'top',
    null,
    [
        'routeTarget' => \Typo3OnAws\AwsAbout\Controller\AboutController::class . '::indexAction',
        'access' => 'user,group',
        'name' => 'help_AwsAbout',
        'icon' => 'EXT:aws_about/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:aws_about/Resources/Private/Language/Modules/aws_about.xlf'
    ]
);
