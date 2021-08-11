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

namespace TYPO3\CMS\Core\Tests\Acceptance\Backend\FileList;

use TYPO3\CMS\Core\Tests\Acceptance\Support\BackendTester;
use TYPO3\CMS\Core\Tests\Acceptance\Support\Helper\FileTree;

/**
 * Cases concerning sys_file_metadata records
 */
class FileClipboardCest extends AbstractFileCest
{
    protected string $copyModeCopy = '#clipboard-copymode-copy';
    protected string $copyModeMove = '#clipboard-copymode-move';

    /**
     * @param BackendTester $I
     */
    public function _before(BackendTester $I, FileTree $tree)
    {
        parent::_before($I, $tree);
        $I->click('#checkClipBoard');
    }

    /**
     * @param BackendTester $I
     */
    public function seeSwitchModes(BackendTester $I)
    {
        $I->seeCheckboxIsChecked($this->copyModeMove);
        $I->dontSeeCheckboxIsChecked($this->copyModeCopy);
        $I->click('//*/label[@for="clipboard-copymode-copy"]');
        $I->dontSeeCheckboxIsChecked($this->copyModeMove);
        $I->seeCheckboxIsChecked($this->copyModeCopy);
    }

    /**
     * @param BackendTester $I
     */
    public function seeAddRemoveSingleRecord(BackendTester $I)
    {
        $fileName = 'bus_lane.jpg';
        $I->switchToMainFrame();
        $I->click('//*[text()="styleguide"]');
        $I->switchToContentFrame();
        $this->openActionDropdown($I, $fileName)->click();
        $this->getActionByTitle($I, $fileName, 'Cut')->click();
        $I->see($fileName, '#clipboard_form');
        $I->click('#clipboard_form a[title="Remove item"]');
    }

    /**
     * @param BackendTester $I
     */
    public function seeAddRemoveMultipleRecords(BackendTester $I)
    {
        $expectedFiles = ['bus_lane.jpg', 'telephone_box.jpg', 'underground.jpg'];

        $I->switchToMainFrame();
        $I->click('//*[text()="styleguide"]');
        $I->switchToContentFrame();

        $I->amGoingTo('add multiple elements to clipboard');
        $I->click('Clipboard #1 (multi-selection mode)');
        $I->click('.t3js-toggle-all-checkboxes');
        $I->click('span[title="Transfer the selection of files to clipboard"]');

        foreach ($expectedFiles as $file) {
            $I->see($file, '#clipboard_form');
        }

        $I->amGoingTo('remove all elements from clipboard');
        $I->click('a[title="Clear"]');
        foreach ($expectedFiles as $file) {
            $I->dontSee($file, '#clipboard_form');
        }
    }
}
