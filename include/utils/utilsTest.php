<?php
/*************************************************************************************************
 * Copyright 2016 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Tests.
 * The MIT License (MIT)
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software
 * and associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *************************************************************************************************/
use PHPUnit\Framework\TestCase;

class testutils extends TestCase {

	/**
	 * Method to_htmlProvider
	 * params
	 */
	public function to_htmlProvider() {
		return array(
			array('Normal string',false,'Normal string','normal string'),
			array('Numbers 012-345,678.9',false,'Numbers 012-345,678.9','Numbers 012-345,678.9'),
			array('Special character string áçèñtös ÑÇ',false,'Special character string &aacute;&ccedil;&egrave;&ntilde;t&ouml;s &Ntilde;&Ccedil;','Special character string with áçèñtös'),
			array('!"·$%&/();,:.=?¿*_-|@#€',false,'!&quot;&middot;$%&amp;/();,:.=?&iquest;*_-|@#&euro;','special string with symbols'),
			array('Greater > Lesser < ',false,'Greater &gt; Lesser &lt; ','Greater > Lesser <(space)'),
			array('Greater > Lesser <',false,'Greater &gt; Lesser &lt;','Greater > Lesser <'),
			array('> Greater > Lesser < ',false,'&gt; Greater &gt; Lesser &lt; ','> Greater Lesser <(space)'),
			array('"\'',false,'&quot;&#039;','special string with quotes'),
			array('<b>Bold HTML</b>',false,'&lt;b&gt;Bold HTML&lt;/b&gt;','Bold HTML'),
			array('<td width="25%" align="right" class="dvtCellLabel"><input type="hidden" value="1" id="hdtxt_IsAdmin">Modified Time</td>',false,'&lt;td width=&quot;25%&quot; align=&quot;right&quot; class=&quot;dvtCellLabel&quot;&gt;&lt;input type=&quot;hidden&quot; value=&quot;1&quot; id=&quot;hdtxt_IsAdmin&quot;&gt;Modified Time&lt;/td&gt;','HTML table cell'),
			array('&amp;&aacute;&lt;&gt;&ntilde;',false,'&amp;amp;&amp;aacute;&amp;lt;&amp;gt;&amp;ntilde;','HTML Codes'),

			array('Normal string (T)',true,'Normal string (T)','normal string (true)'),
			array('Numbers 012-345,678.9 (T)',true,'Numbers 012-345,678.9 (T)','Numbers 012-345,678.9 (true)'),
			array('Special character string áçèñtös ÑÇ (T)',true,'Special character string &aacute;&ccedil;&egrave;&ntilde;t&ouml;s &Ntilde;&Ccedil; (T)','Special character string with áçèñtös (true)'),
			array('!"·$%&/();,:.=?¿*_-|@#€ (T)',true,'!&quot;&middot;$%&amp;/();,:.=?&iquest;*_-|@#&euro; (T)','special string with symbols (true)'),
			array('Greater > Lesser (T) < ',true,'Greater &gt; Lesser (T) &lt; ','Greater > Lesser <(space) (true)'),
			array('Greater > Lesser (T) <',true,'Greater &gt; Lesser (T) &lt;','Greater > Lesser < (true)'),
			array('> Greater > Lesser (T) < ',true,'&gt; Greater &gt; Lesser (T) &lt; ','> Greater Lesser <(space) (true)'),
			array('"\' (T)',true,'&quot;&#039; (T)','special string with quotes (true)'),
			array('<b>Bold HTML (T)</b>',true,'&lt;b&gt;Bold HTML (T)&lt;/b&gt;','Bold HTML (true)'),
			array('<td width="25%" align="right" class="dvtCellLabel"><input type="hidden" value="1" id="hdtxt_IsAdmin">Modified Time</td> (T)',true,'&lt;td width=&quot;25%&quot; align=&quot;right&quot; class=&quot;dvtCellLabel&quot;&gt;&lt;input type=&quot;hidden&quot; value=&quot;1&quot; id=&quot;hdtxt_IsAdmin&quot;&gt;Modified Time&lt;/td&gt; (T)','HTML table cell (true)'),
			array('&amp;&aacute;&lt;&gt;&ntilde; (T)',true,'&amp;amp;&amp;aacute;&amp;lt;&amp;gt;&amp;ntilde; (T)','HTML Codes (true)'),
		);
	}

	/**
	 * Method testto_html
	 * @test
	 * @dataProvider to_htmlProvider
	 */
	public function testto_html($input, $ignore, $expected, $message) {
		$actual = to_html($input, $ignore);
		$this->assertEquals($expected, $actual, "testto_html $message");
	}

	/**
	 * Method testisRecordExists
	 * @test
	 */
	public function testisRecordExists() {
		global $adb;
		$accountWS = vtws_getEntityId('Accounts');
		$userWS = vtws_getEntityId('Users');
		// Not Deleted
		$actual = isRecordExists(80);
		$this->assertEquals(true, $actual, "testisRecordExists not deleted CRM record");
		$actual = isRecordExists(5);
		$this->assertEquals(true, $actual, "testisRecordExists not deleted User record");
		$actual = isRecordExists($accountWS.'x80');
		$this->assertEquals(true, $actual, "testisRecordExists deleted CRM record Webservice");
		$actual = isRecordExists($userWS.'x5');
		$this->assertEquals(true, $actual, "testisRecordExists deleted User record Webservice");
		// Deleted Records
		$adb->query('update vtiger_crmentity set deleted=1 where crmid=80');
		$adb->query('update vtiger_users set deleted=1 where id=5');
		$actual = isRecordExists(80);
		$this->assertEquals(false, $actual, "testisRecordExists deleted CRM record");
		$actual = isRecordExists(5);
		// THIS ONE IS WRONG BECAUSE WE CANNOT DISTINGUISH A USER FROM A NORMAL CRM RECORD SO WE FIND cbupdater 5 and return true
		$this->assertEquals(true, $actual, "testisRecordExists deleted User record");
		$actual = isRecordExists(1);
		// THIS ONE IS WRONG, IT RETURNS FALSE BECAUSE THERE IS NO RECORD 1 but there is a user 1
		$this->assertEquals(false, $actual, "testisRecordExists deleted User record");
		$actual = isRecordExists($accountWS.'x80');
		$this->assertEquals(false, $actual, "testisRecordExists deleted CRM record Webservice");
		$adb->query('update vtiger_users set deleted=1 where crmid=5');
		$actual = isRecordExists($userWS.'x5');
		$this->assertEquals(false, $actual, "testisRecordExists deleted User record Webservice");
		// restore DB
		$adb->query('update vtiger_crmentity set deleted=0 where crmid=80');
		$adb->query('update vtiger_users set deleted=0 where id=5');
	}

	/**
	 * Method html2utf8Provider
	 * params
	 */
	public function html2utf8Provider() {
		return array(
			array('Normal string','Normal string','normal string'),
			array('Numbers 012-345,678.9','Numbers 012-345,678.9','Numbers 012-345,678.9'),
			array('Special character string áçèñtös ÑÇ','Special character string áçèñtös ÑÇ','Special character string with áçèñtös'),
			array('!"·$%&/();,:.=?¿*_-|@#€','!"·$%&/();,:.=?¿*_-|@#€','special string with symbols'),
			array('Greater > Lesser < ','Greater > Lesser < ','Greater > Lesser <(space)'),
			array('Greater > Lesser <','Greater > Lesser <','Greater > Lesser <'),
			array('&quot;&#039;','&quot;&#039;','special string with quotes'),
			array('<p>I will display &spades;</p>','<p>I will display &spades;</p>','<p>I will display &spades;</p>'),
			array('<p>I will display &#9824;</p>','<p>I will display ♠</p>','<p>I will display &#9824;</p>'),
			array('<p>I will display &#x2660;</p>','<p>I will display &#x2660;</p>','<p>I will display &#x2660;</p>'),
		);
	}

	/**
	 * Method testhtml_to_utf8
	 * @test
	 * @dataProvider html2utf8Provider
	 */
	public function testhtml_to_utf8($data, $expected, $message) {
		$actual = html_to_utf8($data);
		$this->assertEquals($expected, $actual, "testhtml_to_utf8 $message");
	}

	/**
	 * Method getValidDBInsertDateValueProvider
	 * params
	 */
	public function getValidDBInsertDateValueProvider() {
		return array(
			array(7,'2016-06-01','2016-06-01','Fdom testymd'),
			array(7,'2016-06-15','2016-06-15','Mdom testymd'),
			array(7,'2016-06-30','2016-06-30','Ldom testymd'),
			array(5,'01-06-2016','2016-06-01','Fdom testdmy'),
			array(5,'15-06-2016','2016-06-15','Mdom testdmy'),
			array(5,'30-06-2016','2016-06-30','Ldom testdmy'),
			array(6,'06-01-2016','2016-06-01','Fdom testmdy'),
			array(6,'06-15-2016','2016-06-15','Mdom testmdy'),
			array(6,'06-30-2016','2016-06-30','Ldom testmdy'),
			array(7,'1997-03-04','1997-03-04','F1997-03-04 testymd'),
			array(5,'04-03-1997','1997-03-04','F04-03-1997 testdmy'),
			array(6,'03-04-1997','1997-03-04','F03-04-1997 testmdy'),
			///////////////////
			array(7,'2016.06.01','2016-06-01','Fdom testymd'),
			array(7,'2016.06.15','2016-06-15','Mdom testymd'),
			array(7,'2016.06.30','2016-06-30','Ldom testymd'),
			array(5,'01.06.2016','2016-06-01','Fdom testdmy'),
			array(5,'15.06.2016','2016-06-15','Mdom testdmy'),
			array(5,'30.06.2016','2016-06-30','Ldom testdmy'),
			array(6,'06.01.2016','2016-06-01','Fdom testmdy'),
			array(6,'06.15.2016','2016-06-15','Mdom testmdy'),
			array(6,'06.30.2016','2016-06-30','Ldom testmdy'),
			array(7,'2016/06/01','2016-06-01','Fdom testymd'),
			array(7,'2016/06/15','2016-06-15','Mdom testymd'),
			array(7,'2016/06/30','2016-06-30','Ldom testymd'),
			array(5,'01/06/2016','2016-06-01','Fdom testdmy'),
			array(5,'15/06/2016','2016-06-15','Mdom testdmy'),
			array(5,'30/06/2016','2016-06-30','Ldom testdmy'),
			array(6,'06/01/2016','2016-06-01','Fdom testmdy'),
			array(6,'06/15/2016','2016-06-15','Mdom testmdy'),
			array(6,'06/30/2016','2016-06-30','Ldom testmdy'),
			array(7,'2016/06/1','2016-06-01','Fdom testymd'),
			array(7,'2016/6/15','2016-06-15','Mdom testymd'),
			array(5,'1/06/2016','2016-06-01','Fdom testdmy'),
			array(5,'15/6/2016','2016-06-15','Mdom testdmy'),
			array(6,'06/1/2016','2016-06-01','Fdom testmdy'),
			array(6,'6/15/2016','2016-06-15','Mdom testmdy'),
		);
	}

	/**
	 * Method testgetValidDBInsertDateValue
	 * @test
	 * @dataProvider getValidDBInsertDateValueProvider
	 */
	public function testgetValidDBInsertDateValue($user, $data, $expected, $message) {
		global $current_user;
		$holduser = $current_user;
		$dtuser = new Users();
		$dtuser->retrieveCurrentUserInfoFromFile($user);
		$current_user = $dtuser;
		$actual = getValidDBInsertDateValue($data);
		$this->assertEquals($expected, $actual, "getValidDBInsertDateValue $message");
		$current_user = $holduser;
	}

	/**
	 * Method testgetValidDBInsertDateTimeValue
	 * @test
	 */
	public function testgetValidDBInsertDateTimeValue() {
		// getValidDBInsertDateTimeValue is tested with the testgetValidDBInsertDateValue and DateTimeField class tests
		$this->assertTrue(true);
	}

	/**
	 * Method testgetMailFields
	 * @test
	 */
	public function testgetMailFields() {
		$emailfields = array(
			'tablename' => 'vtiger_account',
			'fieldname' => 'email1',
			'fieldlabel' => 'Email',
		);
		$this->assertEquals($emailfields, getMailFields(getTabid('Accounts')), 'Account Email Field');
		$emailfields = array(
			'tablename' => 'vtiger_contactdetails',
			'fieldname' => 'email',
			'fieldlabel' => 'Email',
		);
		$this->assertEquals($emailfields, getMailFields(getTabid('Contacts')), 'Contact Email Field');
		$emailfields = array(
			'tablename' => 'vtiger_users',
			'fieldname' => 'email1',
			'fieldlabel' => 'Email',
		);
		$this->assertEquals($emailfields, getMailFields(getTabid('Users')), 'Users Email Field');
		$this->assertEquals(array(), getMailFields(getTabid('Assets')), 'Assets Email Fields');
	}

	/**
	 * Method testgetModuleForField
	 * @test
	 */
	public function testgetModuleForField() {
		$this->assertEquals('Calendar', getModuleForField(254), 'Calendar Field');
		$this->assertEquals('Contacts', getModuleForField(100), 'Contact Field');
		$this->assertEquals('Users', getModuleForField(482), 'Users Email Field');
		$this->assertEquals('Users', getModuleForField(-1), 'Users Email Address specification');
	}

	/**
	 * Method testgetCurrentModule
	 * @test
	 */
	public function testgetCurrentModule() {
		global $currentModule;
		$this->assertEquals($currentModule, getCurrentModule(), 'getCurrentModule default');
		$holdcurrentModule = $currentModule;
		$currentModule = '';
		$_REQUEST['module'] = 'doesnotexist';
		$this->assertNull(getCurrentModule(), 'getCurrentModule not exist');
		$_REQUEST['module'] = '../../Accounts';
		$this->assertNull(getCurrentModule(), 'getCurrentModule incorrect');
		$_REQUEST['module'] = 'Accounts';
		$this->assertEquals('Accounts', getCurrentModule(), 'getCurrentModule Accounts no set');
		$this->assertEmpty($currentModule, 'getCurrentModule accounts currentmodule not set');
		$this->assertEquals('Accounts', getCurrentModule(true), 'getCurrentModule Accounts set');
		$this->assertEquals('Accounts', $currentModule, 'getCurrentModule accounts currentmodule set');
		$currentModule = $holdcurrentModule;
	}

	/**
	 * Method testget_themes
	 * @test
	 */
	public function testget_themes() {
		$expected = array(
			'alphagrey' => 'alphagrey',
			'bluelagoon' => 'bluelagoon',
			'softed' => 'softed',
			'woodspice' => 'woodspice',
		);
		$this->assertEquals($expected, get_themes(), 'get_themes');
	}

	/**
	 * Method testisValueInPicklist
	 * @test
	 */
	public function testisValueInPicklist() {
		$this->assertTrue(isValueInPicklist('Banking', 'industry'), 'isValueInPicklist 1');
		$this->assertTrue(isValueInPicklist('In Service', 'assetstatus'), 'isValueInPicklist 1');
		$this->assertFalse(isValueInPicklist('NOTINBanking', 'industry'), 'isValueInPicklist 1');
		$this->assertFalse(isValueInPicklist('NOTInService', 'assetstatus'), 'isValueInPicklist 1');
	}

	/**
	 * Method getDuplicateQueryProvider
	 * params
	 */
	public function getDuplicateQueryProvider() {
		return array(
			array(
				'cbtranslation',
				'vtiger_cbtranslation.i18n.i18n,vtiger_cbtranslation.locale.locale',
				array('i18n'=> '19', 'locale'=> '32'),
				"SELECT vtiger_cbtranslation.cbtranslationid AS recordid, vtiger_users_last_import.deleted,vtiger_cbtranslation.i18n,vtiger_cbtranslation.locale FROM vtiger_cbtranslation INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cbtranslation.cbtranslationid INNER JOIN vtiger_cbtranslationcf ON vtiger_cbtranslationcf.cbtranslationid = vtiger_cbtranslation.cbtranslationid LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_cbtranslation.cbtranslationid INNER JOIN (SELECT vtiger_cbtranslation.i18n,vtiger_cbtranslation.locale  FROM vtiger_cbtranslation INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_cbtranslation.cbtranslationid INNER JOIN vtiger_cbtranslationcf ON vtiger_cbtranslationcf.cbtranslationid = vtiger_cbtranslation.cbtranslationid LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid  WHERE vtiger_crmentity.deleted = 0 GROUP BY vtiger_cbtranslation.i18n,vtiger_cbtranslation.locale HAVING COUNT(*)>1) AS temp ON  ifnull(vtiger_cbtranslation.i18n,'null') = ifnull(temp.i18n,'null') and  ifnull(vtiger_cbtranslation.locale,'null') = ifnull(temp.locale,'null') WHERE vtiger_crmentity.deleted = 0 ORDER BY vtiger_cbtranslation.i18n,vtiger_cbtranslation.locale,vtiger_cbtranslation.cbtranslationid ASC",
			),
			array(
				'Assets',
				'vtiger_assets.product.product,vtiger_assets.serialnumber.serialnumber',
				array('product'=> '10', 'serialnumber'=> '1'),
				"SELECT vtiger_assets.assetsid AS recordid, vtiger_users_last_import.deleted,vtiger_assets.product,vtiger_assets.serialnumber FROM vtiger_assets INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_assets.assetsid INNER JOIN vtiger_assetscf ON vtiger_assetscf.assetsid = vtiger_assets.assetsid LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_assets.assetsid INNER JOIN (SELECT vtiger_assets.product,vtiger_assets.serialnumber  FROM vtiger_assets INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_assets.assetsid INNER JOIN vtiger_assetscf ON vtiger_assetscf.assetsid = vtiger_assets.assetsid LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid  WHERE vtiger_crmentity.deleted = 0 GROUP BY vtiger_assets.product,vtiger_assets.serialnumber HAVING COUNT(*)>1) AS temp ON  ifnull(vtiger_assets.product,'null') = ifnull(temp.product,'null') and  ifnull(vtiger_assets.serialnumber,'null') = ifnull(temp.serialnumber,'null') WHERE vtiger_crmentity.deleted = 0 ORDER BY vtiger_assets.product,vtiger_assets.serialnumber,vtiger_assets.assetsid ASC",
			),
			array(
				'Contacts',
				'vtiger_contactdetails.firstname.firstname,vtiger_contactsubdetails.email2.email2',
				array('firstname'=> '1', 'email2'=> '13'),
"SELECT vtiger_contactdetails.contactid AS recordid,
				vtiger_users_last_import.deleted,vtiger_contactdetails.firstname,vtiger_contactsubdetails.email2
				FROM vtiger_contactdetails
				INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_contactdetails.contactid
				INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
				INNER JOIN vtiger_contactsubdetails ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
				LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
				LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contactdetails.accountid
				LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
				LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
				LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
				INNER JOIN (SELECT vtiger_contactdetails.firstname,vtiger_contactsubdetails.email2
						FROM vtiger_contactdetails
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_contactdetails.contactid
						INNER JOIN vtiger_contactaddress ON vtiger_contactdetails.contactid = vtiger_contactaddress.contactaddressid
						INNER JOIN vtiger_contactsubdetails ON vtiger_contactaddress.contactaddressid = vtiger_contactsubdetails.contactsubscriptionid
						LEFT JOIN vtiger_contactscf ON vtiger_contactscf.contactid = vtiger_contactdetails.contactid
						LEFT JOIN vtiger_account ON vtiger_account.accountid=vtiger_contactdetails.accountid
						LEFT JOIN vtiger_customerdetails ON vtiger_customerdetails.customerid=vtiger_contactdetails.contactid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 
						GROUP BY vtiger_contactdetails.firstname,vtiger_contactsubdetails.email2 HAVING COUNT(*)>1) as temp
					ON  ifnull(vtiger_contactdetails.firstname,'null') = ifnull(temp.firstname,'null') and  ifnull(vtiger_contactsubdetails.email2,'null') = ifnull(temp.email2,'null')
								WHERE vtiger_crmentity.deleted=0  ORDER BY vtiger_contactdetails.firstname,vtiger_contactsubdetails.email2,vtiger_contactdetails.contactid ASC",
			),
			array(
				'Potentials',
				'vtiger_potentials.amount.amount,vtiger_potentials.forecast.forecast',
				array('amount'=> '71', 'forecast'=> '9'),
				"SELECT vtiger_potential.potentialid AS recordid,
			vtiger_users_last_import.deleted,vtiger_potentials.amount,vtiger_potentials.forecast
			FROM vtiger_potential
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_potential.potentialid
			LEFT JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
			LEFT JOIN vtiger_users_last_import ON vtiger_users_last_import.bean_id=vtiger_potential.potentialid
			LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
			LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
			INNER JOIN (SELECT vtiger_potentials.amount,vtiger_potentials.forecast
						FROM vtiger_potential
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_potential.potentialid
						LEFT JOIN vtiger_potentialscf ON vtiger_potentialscf.potentialid = vtiger_potential.potentialid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						WHERE vtiger_crmentity.deleted=0 
						GROUP BY vtiger_potentials.amount,vtiger_potentials.forecast HAVING COUNT(*)>1) as temp
			ON  ifnull(vtiger_potentials.amount,'null') = ifnull(temp.amount,'null') and  ifnull(vtiger_potentials.forecast,'null') = ifnull(temp.forecast,'null')
							WHERE vtiger_crmentity.deleted=0  ORDER BY vtiger_potentials.amount,vtiger_potentials.forecast,vtiger_potential.potentialid ASC",
			),
		);
	}

	/**
	 * Method testgetDuplicateQuery
	 * @test
	 * @dataProvider getDuplicateQueryProvider
	 */
	public function testgetDuplicateQuery($module, $field_values, $ui_type_arr, $expected) {
		$actual = getDuplicateQuery($module, $field_values, $ui_type_arr);
		$this->assertEquals($expected, $actual, "Test getDuplicatesQuery Method on $module Module");
	}
	
	/**
	 * Method getProfile2FieldPermissionListProvider
	 * params
	 */
	public function getProfile2FieldPermissionListProvider() {
		$expected_cbtranslation_module_fields = array(
			array('cbtranslation No', '0', '4', '0', '833', '1', 'V~M'),
			array('Locale', '0', '32', '0', '834', '1', 'V~M'),
			array('Module', '0', '1614', '0', '835', '1', 'V~M'),
			array('Key', '0', '1', '0', '836', '1', 'V~M'),
			array('i18n', '0', '19', '0', '837', '1', 'V~0'),
			array('Translates', '0', '10', '0', '838', '1', 'V~0'),
			array('Picklist', '0', '1615', '0', '839', '1', 'V~0'),
			array('Field', '0', '1', '0', '840', '1', 'V~0'),
			array('Proof Read', '0', '56', '0', '841', '1', 'C~0'),
			array('Assigned To', '0', '53', '0', '842', '1', 'V~M'),
			array('Created Time', '0', '70', '0', '843', '2', 'T~O'),
			array('Modified Time', '0', '70', '0', '844', '2', 'T~O'),
			array('Created By', '0', '52', '0', '845', '2', 'V~O'),
		);
		$expected_accounts_module_fields = array(
			array('Account Name', '0', '2', '0', '1', '1', 'V~M'),
			array('Account No', '0', '4', '0', '2', '1', 'V~O'),
			array('Phone', '0', '11', '0', '3', '1', 'V~O'),
			array('Website', '0', '17', '0', '4', '1', 'V~O'),
			array('Fax', '0', '11', '0', '5', '1', 'V~O'),
			array('Ticker Symbol', '0', '1', '0', '6', '1', 'V~O' ),
			array('Other Phone', '0', '11', '0', '7', '1', 'V~O'),
			array('Member Of', '0', '51', '0', '8', '1', 'I~O'),
			array('Email', '0', '13', '0', '9', '1', 'E~O'),
			array('Employees', '0', '7', '0', '10', '1', 'I~O'),
			array('Other Email', '0', '13', '0', '11', '1', 'E~O'),
			array('Ownership', '0', '1', '0', '12', '1', 'V~O'),
			array('Rating', '0', '15', '0', '13', '1', 'V~O'),
			array('industry', '0', '15', '0', '14', '1', 'V~O'),
			array('SIC Code', '0', '1', '0', '15', '1', 'V~O'),
			array('Type', '0', '15', '0', '16', '1', 'V~O'),
			array('Annual Revenue', '0', '71', '0', '17', '1', 'N~O'),
			array('Email Opt Out', '0', '56', '0', '18', '1','C~O'),
			array('Notify Owner', '0', '56', '0', '19', '1', 'C~O'),
			array('Assigned To', '0', '53', '0', '20', '1', 'V~M'),
			array('Created Time', '0', '70', '0', '21', '2', 'DT~O'),
			array('Modified Time', '0', '70', '0', '22', '2', 'DT~O'),
			array('Last Modified By', '0', '52', '0', '23', '3', 'V~O'),
			array('Billing Address','0', '21', '0', '24', '1', 'V~O'),
			array('Shipping Address', '0', '21', '0', '25', '1', 'V~O'),
			array('Billing City', '0', '1', '0', '26', '1', 'V~O'),
			array('Shipping City', '0', '1', '0', '27', '1', 'V~O'),
			array('Billing State', '0', '1', '0', '28', '1', 'V~O'),
			array('Shipping State', '0', '1', '0', '29', '1', 'V~O'),
			array('Billing Code', '0', '1', '0', '30', '1', 'V~O'),
			array('Shipping Code', '0', '1', '0', '31', '1', 'V~O'),
			array('Billing Country', '0', '1', '0', '32', '1', 'V~O'),
			array('Shipping Country', '0', '1', '0', '33', '1', 'V~O'),
			array('Billing Po Box', '0', '1', '0', '34', '1', 'V~O'),
			array('Shipping Po Box', '0', '1', '0', '35', '1', 'V~O'),
			array('Description', '0', '19', '0', '36', '1', 'V~O'),
			array('Status', '0', '16', '0', '152', '1', 'V~O'),
			array('Text', '0', '1', '0', '718', '1', 'V~O~LE~50'),
			array('Number', '0', '7', '0', '719', '1', 'NN~O~8,2'),
			array('Percent', '0', '9', '0', '720', '1', 'N~O~2~2'),
			array('Currency', '0', '71', '0', '721', '1', 'N~O~8,2'),
			array('Date', '0', '5', '0', '722', '1', 'D~O'),
			array('Emailcf', '0', '13', '0', '723', '1', 'E~O'),
			array('Phonecf', '0', '11', '0', '724', '1', 'V~O'),
			array('URL', '0', '17', '0', '725', '1', 'V~O'),
			array('Checkbox', '0', '56', '0', '726', '1', 'C~O'),
			array('skypecf', '0', '85', '0', '727', '1', 'V~O'),
			array('Time', '0', '14', '0', '728', '1', 'T~O'),
			array('PLMain', '0', '15', '0', '729', '1', 'V~O'),
			array('PLDep1', '0', '15', '0', '730', '1', 'V~O'),
			array('PLDep2', '0', '15', '0', '731', '1', 'V~O'),
			array('Planets', '0', '33', '0', '732', '1', 'V~O'),
			array('Is Converted From Lead', '0', '56', '0', '752', '2', 'C~O'),
			array('Converted From Lead', '0', '10', '0', '753', '3', 'V~O'),
			array('Created By', '0', '52', '0', '764', '2', 'V~O'), 
		);
		$expected_contacts_module_fields = array(
			array('Salutation', '0', '55', '0', '66', '3', 'V~O'),
			array('First Name', '0', '55', '0', '67', '1', 'V~O'),
			array('Contact Id', '0', '4', '0', '68', '1', 'V~O'),
			array('Office Phone', '0', '11', '0', '69', '1', 'V~O'),
			array('Last Name', '0', '255', '0', '70', '1', 'V~M'),
			array('Mobile', '0', '11', '0', '71', '1', 'V~O'),
			array('Account Name', '0', '51', '0', '72', '1', 'I~O'),
			array('Home Phone', '0', '11', '0', '73', '1', 'V~O'),
			array('Lead Source', '0', '15', '0', '74', '1', 'V~O'),
			array('Other Phone', '0', '11', '0', '75', '1', 'V~O'),
			array('Title', '0', '1', '0', '76', '1', 'V~O'),
			array('Fax', '0', '11', '0', '77', '1', 'V~O'),
			array('Department', '0', '1', '0', '78', '1', 'V~O'),
			array('Birthdate', '0', '5', '0', '79', '1', 'D~O'),
			array('Email', '0', '13', '0', '80', '1', 'E~O'),
			array('Reports To', '0', '57', '0', '81', '1', 'V~O'),
			array('Assistant', '0', '1', '0', '82', '1', 'V~O'),
			array('Secondary Email', '0', '13', '0', '83', '1', 'E~O'),
			array('Assistant Phone', '0', '11', '0', '84', '1', 'V~O'),
			array('Do Not Call', '0', '56', '0', '85', '1', 'C~O'),
			array('Email Opt Out', '0', '56', '0', '86', '1', 'C~O'),
			array('Assigned To', '0', '53', '0', '87', '1', 'V~M'),
			array('Reference', '0', '56', '0', '88', '1', 'C~O'),
			array('Notify Owner', '0', '56', '0', '89', '1', 'C~O'),
			array('Created Time', '0', '70', '0', '90', '2', 'DT~O'),
			array('Modified Time', '0', '70', '0', '91', '2', 'DT~O'),
			array('Last Modified By', '0', '52', '0', '92', '3', 'V~O'),
			array('Portal User', '0', '56', '0', '93', '1', 'C~O'),
			array('Support Start Date', '0', '5', '0', '94', '1', 'D~O'),
			array('Support End Date', '0', '5', '0', '95', '1', 'D~O~OTH~GE~support_start_date~Support Start Date'),
			array('Mailing Street', '0', '21', '0', '96', '1', 'V~O'),
			array('Other Street', '0', '21', '0', '97', '1', 'V~O'),
			array('Mailing City', '0', '1', '0', '98', '1', 'V~O'),
			array('Other City', '0', '1', '0', '99', '1', 'V~O'),
			array('Mailing State', '0', '1', '0', '100', '1', 'V~O'),
			array('Other State', '0', '1', '0', '101', '1', 'V~O'),
			array('Mailing Zip', '0', '1', '0', '102', '1', 'V~O'),
			array('Other Zip', '0', '1', '0', '103', '1', 'V~O'),
			array('Mailing Country', '0', '1', '0', '104', '1', 'V~O'),
			array('Other Country', '0', '1', '0', '105', '1', 'V~O'),
			array('Mailing Po Box', '0', '1', '0', '106', '1', 'V~O'),
			array('Other Po Box', '0', '1', '0', '107', '1', 'V~O'),
			array('Contact Image', '0', '69', '0', '108', '1', 'V~O'),
			array('Description', '0', '19', '0', '109', '1', 'V~O'),
			array('Status', '0', '16', '0', '151', '1', 'V~O'),
			array('Is Converted From Lead', '0', '56', '0', '754', '2', 'C~O'),
			array('Converted From Lead', '0', '10', '0', '755', '3', 'V~O'),
			array('Created By', '0', '52', '0', '763', '2', 'V~O'),
		);
		return array(
			array('cbtranslation', '1', $expected_cbtranslation_module_fields),
			array('cbtranslation', '2', $expected_cbtranslation_module_fields),
			array('cbtranslation', '3', $expected_cbtranslation_module_fields),
			array('cbtranslation', '4', $expected_cbtranslation_module_fields),
			array('cbtranslation', '5', $expected_cbtranslation_module_fields),
			array('cbtranslation', '6', $expected_cbtranslation_module_fields),
			array('Accounts', '1', $expected_accounts_module_fields),
			array('Accounts', '2', $expected_accounts_module_fields),
			array('Accounts', '3', $expected_accounts_module_fields),
			array('Accounts', '4', $expected_accounts_module_fields),
			array('Accounts', '5', $expected_accounts_module_fields),
			array('Accounts', '6', $expected_accounts_module_fields),
			array('Contacts', '1', $expected_contacts_module_fields),
			array('Contacts', '2', $expected_contacts_module_fields),
			array('Contacts', '3', $expected_contacts_module_fields),
			array('Contacts', '4', $expected_contacts_module_fields),
			array('Contacts', '5', $expected_contacts_module_fields),
			array('Contacts', '6', $expected_contacts_module_fields),
		);
	}

	/**
	 * Method testgetProfile2FieldPermissionList
	 * @test
	 * @dataProvider getProfile2FieldPermissionListProvider
	 */
	public function testgetProfile2FieldPermissionList($module, $profileid, $expected) {
		$actual = getProfile2FieldPermissionList($module, $profileid);
		$this->assertEquals($expected, $actual, "Test getProfile2FieldPermissionList Method on $module Module and Profileid $profileid");
	}
}
