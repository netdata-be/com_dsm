Hoe werkt de sync module
========================

Er staat een XML op de server: _/data/sites/web/wine-budgetcom/sync/art2si.xml_

XML wordt geparsed in views import _view.html.php_
Hier wordt een array opgebouwd van de products met hun data.
Er zijn enkele velden die 'aangepast' worden door de parser.

* een custom field PRIJS wordt aangemaakt afhankelijk van de product prijs (5-10 euro / 10-15 euro ... )
* Aantal stuks die per order minimaal mogen besteld worden: alles wordt per 6 flessen verkocht, behalve proefpakketten.
* 21% BTW wordt bij de prijs bijgeteld
* Custom field ID mapping gebeurd ook hier.

Vervolgens gebeuren er een aantal stappen:

* LoadProducts ($products, $load_datetime)
 - Maak de table _dsm_staging_products
 - Smijt oude products weg (ouder dan 4 dagen )
 - loop over alle products en steek deze in de staging table
 - test of het aantal products in de XML evereenkomt met het een aantal aangemaakte records in mysql
 - zet load_status naar `loaded`

* PrepareLoad($load_datetime)
 - Test welke products reeds bestaan in virtuemart en verzet de load_status naar `to update`
 - Test welke products nog niet bestaan in virtuemart en verzet de load_status naar `to insert`

* ImportProducts($load_datetime,$language_tables_suffixes,$custom_fields)
 - Maak producten aan in virtuemart waarvan de load_status `to insert` is
 - Maak nieuwe product names aan indien ze niet bestaan
 - Maak nieuwe prijzen aan 
 - Smijt alle custom fields weg !
 - en maak ze terug aan
 - maak een `related products` aan gebaseed op de custom field smaak
 - Maak manufactures aan
 - link alle producten aan deze manufactures 
 
* UpdateProducts($load_datetime,$language_tables_suffixes,$custom_fields)
 - Voor alle product id's die niet meer in de XML zitten zetten we `published` op 0
 - update products
 - update product prijzen
 - Update prduct namen in alle talen
 - !! Custom fields worden *NIET* geupdate...

* UpdateProductCategories()
 - Smijt alle product categories weg
 - Genereer de categories opnieuw gebaseerd op custom fields

* UpdateProductRatings($load_datetime)
 - Smijt alle product ratings weg
 - Maak alle ratings terug aan (zitten mee in XML )
