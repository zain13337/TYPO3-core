<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace TYPO3\CMS\Core\Tests\Acceptance\Support\Helper;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use TYPO3\CMS\Core\Tests\Acceptance\Support\ApplicationTester;

final class PageTree extends AbstractTree
{
    public static $treeSelector = '#typo3-pagetree-treeContainer';

    private Mouse $mouse;

    /**
     * Inject our core AcceptanceTester actor into PageTree
     */
    public function __construct(ApplicationTester $I, Mouse $mouse)
    {
        $this->tester = $I;
        $this->mouse = $mouse;
    }

    /**
     * Perform drag and drop for a new page into the given target page.
     */
    public function dragAndDropNewPage(string $pageName, string $dragNode, string $nodeEditInput): void
    {
        $target = $this->getPageXPathByPageName($pageName);
        $pageTitle = sprintf('Dummy 1-%s-new', $pageName);

        $this->mouse->dragAndDrop($dragNode, $target);

        $this->tester->seeElement($nodeEditInput);

        // Change the new page title.
        // We can't use $I->fillField() here since this sends a clear() to the element
        // which drops the node creation in the tree. So we do it manually with selenium.
        $element = $this->tester->executeInSelenium(static function (RemoteWebDriver $webdriver) use ($nodeEditInput) {
            return $webdriver->findElement(WebDriverBy::cssSelector($nodeEditInput));
        });
        $element->sendKeys($pageTitle);

        $this->tester->pressKey($nodeEditInput, WebDriverKeys::ENTER);
        $this->tester->waitForElementNotVisible($nodeEditInput);
        $this->tester->waitForText($pageTitle);
    }

    /**
     * Get node identifier of given page.
     */
    public function getPageXPathByPageName(string $pageName): string
    {
        return '//*[@class="node-name" and text()=\'' . $pageName . '\']/..';
    }
}
