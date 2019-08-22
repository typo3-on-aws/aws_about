<?php
namespace Typo3OnAws\AwsAbout\Controller;

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

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplate;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3Fluid\Fluid\View\ViewInterface;

/**
 * Module 'about' shows some standard information for TYPO3 CMS.
 * This class is based on the system extension EXT:about
 */
class AboutController
{
    /**
     * ModuleTemplate object
     *
     * @var ModuleTemplate
     */
    protected $moduleTemplate;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Main action: Show standard information
     *
     * @return ResponseInterface the HTML output
     */
    public function indexAction(): ResponseInterface
    {
        $this->moduleTemplate = GeneralUtility::makeInstance(ModuleTemplate::class);
        $this->initializeView('index');
        $warnings = [];
        // Hook for additional warnings
        foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['displayWarningMessages'] ?? [] as $className) {
            $hookObj = GeneralUtility::makeInstance($className);
            if (method_exists($hookObj, 'displayWarningMessages_postProcess')) {
                $hookObj->displayWarningMessages_postProcess($warnings);
            }
        }

        $this->view->assignMultiple([
            'currentVersion' => TYPO3_version,
            'awsExtensions' => $this->getLoadedAwsExtensions(),
            'warnings' => $warnings,
        ]);

        $this->moduleTemplate->setContent($this->view->render());
        return new HtmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * Fetches a list of all active (loaded) extensions in the current system
     *
     * @return array
     */
    protected function getLoadedAwsExtensions(): array
    {
        $extensions = [];
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        foreach ($packageManager->getAvailablePackages() as $package) {
            // Skip system extensions (= type: typo3-cms-framework)
            if ($package->getValueFromComposerManifest('type') !== 'typo3-cms-extension') {
                continue;
            }
            if (substr($package->getPackageKey(), 0, 4) == 'aws_') {
                $extensions[] = [
                    'key' => $package->getPackageKey(),
                    'title' => $this->convertExtensionKeyToTitle($package->getPackageKey()),
                    'description' => $package->getPackageMetaData()->getDescription(),
                    'version' => $package->getPackageMetaData()->getVersion(),
                    //'meta' => $package->getPackageMetaData(),
                    //'package' => $package,
                    'icon' => $this->getExtensionIcon($package->getPackageKey()),
                    'authors' => $package->getValueFromComposerManifest('authors'),
                    'homepage' => $package->getValueFromComposerManifest('homepage'),
                    'active' => ($this->isActive($package->getPackageKey()) ? true : false)
                ];
            }
        }
        return $extensions;
    }

    /**
     * Shortcut method to check if an extension is loaded
     *
     * @param string $extensionKey
     * @return bool
     */
    protected function isActive(string $extensionKey): bool
    {
        return ExtensionManagementUtility::isLoaded($extensionKey);
    }

    /**
     * Converts an extension key to a human-readable title
     *
     * @param string $extensionKey
     * @return string
     */
    protected function convertExtensionKeyToTitle(string $extensionKey): string
    {
        return preg_replace('/^Aws/', 'AWS', ucwords(str_replace('_', ' ', $extensionKey)));
    }

    /**
     * Returns the absolutely file system path to the Extension icon, if extension is loaded and an icon exists
     *
     * @param string $extensionKey
     * @return string|null
     */
    protected function getExtensionIcon(string $extensionKey): ?string
    {
        if ($this->isActive($extensionKey) === false) {
            return null;
        }

        $path = ExtensionManagementUtility::extPath($extensionKey);
        foreach (['svg', 'png', 'gif'] as $fileExtension) {
            $iconFile = $path . 'Resources/Public/Icons/Extension.' . $fileExtension;
            if (is_readable($iconFile)) {
                return 'EXT:' . $extensionKey . '/Resources/Public/Icons/Extension.' . $fileExtension;
            }
        }
        return null;
    }

    /**
     * Initializes the view by setting the templateName
     *
     * @param string $templateName
     */
    protected function initializeView(string $templateName): void
    {
        $this->view = GeneralUtility::makeInstance(StandaloneView::class);
        $this->view->setTemplate($templateName);
        $this->view->setTemplateRootPaths(['EXT:aws_about/Resources/Private/Templates/About']);
        $this->view->setPartialRootPaths(['EXT:aws_about/Resources/Private/Partials']);
        $this->view->setLayoutRootPaths(['EXT:aws_about/Resources/Private/Layouts']);
    }
}
