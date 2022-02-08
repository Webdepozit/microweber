<?php

namespace Tests\Browser\Multilanguage;

use Laravel\Dusk\Browser;
use Tests\Browser\Components\AdminContentMultilanguage;
use Tests\Browser\Components\AdminLogin;
use Tests\Browser\Components\ChekForJavascriptErrors;
use Tests\Browser\Components\FrontendSwitchLanguage;
use Tests\Browser\Components\LiveEditModuleAdd;
use Tests\Browser\Components\LiveEditSwitchLanguage;
use Tests\DuskTestCase;

class LiveEditMultilanguageTest extends DuskTestCase
{
    public $siteUrl = 'http://127.0.0.1:8000/';

    public function testLiveEditNewPageSave()
    {
        $siteUrl = $this->siteUrl;

        $this->browse(function (Browser $browser) use ($siteUrl) {

            $browser->within(new AdminLogin, function ($browser) {
                $browser->fillForm();
            });

            $browser->within(new AdminContentMultilanguage(), function ($browser) {
                $browser->addLanguage('bg_BG');
                $browser->addLanguage('en_US');
            });

            $browser->visit($siteUrl . '?editmode=y');
            $browser->pause(4000);

            $browser->within(new ChekForJavascriptErrors(), function ($browser) {
                 $browser->validate();
            });

            $pageUrl = 'rand-page-multilanguage-'.time();
            $browser->visit($siteUrl . $pageUrl);

            $browser->pause(5000);

            $randClassForDagAndDrop = 'rand-class-'.time();
            $browser->script("$('.edit .container').addClass('$randClassForDagAndDrop')");
            $browser->pause(1000);
            $browser->click('.'.$randClassForDagAndDrop);

            $browser->type('.'.$randClassForDagAndDrop,'This is my text on english language');

            $browser->click('#main-save-btn');
            $browser->pause(5000);

            $currentUrl = $browser->driver->getCurrentURL();
            $slug = mw()->permalink_manager->slug($currentUrl, 'page');
            $this->assertEquals($pageUrl, $slug);


            // Switch to Bulgarian
            $browser->pause(1000);
            $browser->within(new LiveEditSwitchLanguage(), function ($browser) {
                $browser->switchLanguage('bg_BG');
            });

            $currentUrl = $browser->driver->getCurrentURL();
            $this->assertEquals(site_url() . 'bg_BG/' . $pageUrl, $currentUrl);

            $browser->pause(4000);
            $browser->click('.'.$randClassForDagAndDrop);
            $browser->pause(1000);
            $browser->type('.'.$randClassForDagAndDrop,'Текст написан на български, това е българска стрнаица');
            $browser->click('#main-save-btn');
            $browser->pause(5000); 






           // $browser->pause(100000);
        });

    }

}
