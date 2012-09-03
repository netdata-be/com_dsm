Er staat een XML op de server: /data/sites/web/wine-budgetcom/sync/art2si.xml

XML wordt geparsed in views import view.html.php
Hier wordt een array opgebouwd van de products met hun data

Vervolgens gebeuren er een aantal stappen:

* LoadProducts ($products, $load_datetime)
 - Maak de table _dsm_staging_products
 - Smijt oude products weg (ouder dan 4 dagen )
 - loop over alle products en steek deze in de staging table
 - test of het aantal products evereenkomt het een aantal aangemaakte records in mysql
 - zet load_status naar "loaded"
 
* PrepareLoad($load_datetime)
 - Test welke products reeds bestaan in virtuemart en verzet de load_status naar "to update"
 - Test welke products nog niet bestaan in virtuemart en verzet de load_status naar "to insert"
 
* ImportProducts($load_datetime,$language_tables_suffixes,$custom_fields)
 - 
