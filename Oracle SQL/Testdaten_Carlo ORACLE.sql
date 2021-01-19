--Stored VIEW:
--SELECT n.NUTZER_ID, n.VORNAME ,f.fahrzeug_marke, f.fahrzeug_status AS
--FROM Nutzer n, Fahrzeug f
--WHERE n.nutzer_id = f.fahrzeughalter_id
--order by nutzer_id asc
--Generate drops for all tables
-- select 'drop table ', table_name, 'cascade constraints;' from user_tables;
--Das ist ein View
--SELECT * FROM meine_fahrzeuge ; 

--Nutzerkonten
INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT)  VALUES('jul.schmetz@mail.mx','123!"aB/');

INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT)  VALUES('humberto839@mail.mx','zvfs784A');      

INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT) VALUES('FRedy@mail.mx','kbsd223$');

INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT)VALUES('Leo@mail.mx', '12345678d');                
--select * from nutzerkonto;

INSERT INTO nutzer(nutzerkonto_id, nutzername, vorname, nachname, mobilnummer, anschrift,Nutzer_status,nutzer_bewertung,saldo)
                VALUES(1, 'Juli21','Julliet', 'Schmetz', '126776212', 
                anschrift_t('Luxem', 
                            '41',
                            '50171', 
                            'Koeln',
                            'Deutschland'),
                             'AKTIV',5,0);
                             
                       
INSERT INTO nutzer(nutzerkonto_id, nutzername, vorname, nachname, mobilnummer, anschrift,Nutzer_status,nutzer_bewertung,saldo)
                VALUES(2, 'Humber082','Hsna12', 'Santos', '126776212', 
                anschrift_t('Zollstock', 
                            '99',
                            '50171', 
                            'Koeln',
                            'Deutschland'),
                             'AKTIV',5,0);                       
INSERT INTO nutzer(nutzerkonto_id, nutzername, vorname, nachname, mobilnummer, anschrift,Nutzer_status,nutzer_bewertung,saldo)
                VALUES(3, 'Frexo21','Fredy', 'Valdes', '126776212', 
                anschrift_t('Colonia Monse', 
                            '34',
                            '00503', 
                            'SS',
                            'El Salvador'),
                            'AKTIV',5,0);                     

INSERT INTO nutzer(nutzerkonto_id, nutzername, vorname, nachname, mobilnummer, anschrift,Nutzer_status,nutzer_bewertung,saldo)
                VALUES(4, 'Leo21','Leo', 'Capinni', '126776212', 
                anschrift_t('Colonia Monse', 
                            '34',
                            '00503', 
                            'SS',
                            'Italien'),
                             'AKTIV',5,0);                     

INSERT INTO Paypal(Paypal_Email, zahlung)VALUES('PaypalJuli@Mega.com', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 1));                
INSERT INTO Paypal(Paypal_Email, zahlung)VALUES('PaypalHumberto@Mega.com', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 2));                
INSERT INTO Paypal(Paypal_Email, zahlung)VALUES('PaypalFRed@Mega.com', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 3));                

INSERT INTO Lastschrift(IBAN, BIC, Bankname, zahlung)VALUES('dvss', 'DDDADC','Koelner Bank', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 1));                
INSERT INTO Lastschrift(IBAN, BIC, Bankname, zahlung)VALUES('FKJKSFKJKSFKJKSFKJKFKJKSFKJKSFS', 'FKJKS','Il bello Banco Italia', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 2));                
INSERT INTO Lastschrift(IBAN, BIC, Bankname, zahlung)VALUES('DEDSDS', 'FK98323','Banco de comercio', Zahlung_t(SEQ_ZLG_ID.NEXTVAL,SYSDATE, 3));                

SELECT p.Zahlung.Zahlung_M_ID,p.zahlung.nutzer_id,p.Zahlung.Zahlung_Datum, p.paypal_email FROM Paypal p;
SELECT l.Zahlung.Zahlung_M_ID,l.zahlung.nutzer_id,l.Zahlung.Zahlung_Datum, l.IBAN, l.BIC, l.Bankname FROM Lastschrift l;
select * from buchung;
            select Zahlung_M_ID  from paypal, buchung where p.zahlung.nutzer_id=b.Fahrer_id;

INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeugkennzeichen, Fahrzeug_marke, Fahrzeug_Preis_Faktor,Fahrzeug_status)
                VALUES(1, 'HDSF21341','Mazda', 0.4, 'VERFUEGBAR');                               
INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeugkennzeichen, Fahrzeug_marke, Fahrzeug_Preis_Faktor,Fahrzeug_status)
                VALUES(1, 'HDSF21341','Toyota', 0.4, 'VERFUEGBAR');                               
INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeugkennzeichen, Fahrzeug_marke, Fahrzeug_Preis_Faktor,Fahrzeug_status)
                VALUES(2, 'HDSF21341','Tesla', 2.1, 'VERFUEGBAR');                
INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeugkennzeichen, Fahrzeug_marke, Fahrzeug_Preis_Faktor,Fahrzeug_status)
                VALUES(3, 'HDSF21341','Avion', 3.33, 'VERFUEGBAR');      
INSERT INTO Fahrzeug(Fahrzeughalter_id, Fahrzeugkennzeichen, Fahrzeug_marke, Fahrzeug_Preis_Faktor,Fahrzeug_status)
                VALUES(1, 'HDSF21341','BMW', 2.4, 'INAKTIV');            
COMMIT;
SET SERVEROUTPUT ON;

--Testfälle für P4 Fahrzeuge_nach_Status
SELECT * FROM fahrzeug order by Fahrzeug_id desc;
execute Fahrzeuge_nach_Status('INAKTIV');
execute Fahrzeuge_nach_Status('RESERVIERT');

-- Testfällen für F1 Tarif_berechnen
--SELECT Tarif_berechnen(-0.1, 0.4) FROM DUAL;
--SELECT Tarif_berechnen(2000, 0.1) FROM DUAL;
--SELECT Tarif_berechnen(3500, 1) FROM DUAL;

--Zeige Nutzer mit Anschriften ausgeklappt
--SELECT n.nutzer_id, n.vorname, n.anschrift.strasse, n.anschrift.Hausnummer, n.anschrift.PLZ, n.anschrift.ort, n.anschrift.land FROM nutzer n;
/*SELECT * FROM nutzer;
select * from fahrzeug order by fahrzeughalter_id asc; 
SELECT * FROM fahrzeug order by Fahrzeug_id desc;
SELECT * FROM rechnung order by rechnung_id desc;
SELECT * FROM buchung order by buchung_id desc;
*/
-- Testfällen für F2 
--SELECT Zeichenkette_Verschluesseln(' ') FROM DUAL;
--SELECT Zeichenkette_Verschluesseln('Passwort1234') FROM DUAL;

-- Testfälle für P2 Fahrzeug_buchen_P
-- Buchungen machen. Parameter(#FahrerID,#FahrzeugID)
-- ACHTUNG: Die Bereechnung der Buchungen berüchsichtig ganze Minuten, 
-- warten Sie mindesten EINE Minute, damit der Betrag >0 ist
EXECUTE Fahrzeug_buchen_P(1,3);
EXECUTE Fahrzeug_buchen_P(2,2);
EXECUTE Fahrzeug_buchen_P(3,4);
EXECUTE Fahrzeug_buchen_P(3,1);

execute Fahrzeuge_nach_Status('RESERVIERT');
SELECT * FROM buchung order by buchung_id desc;

-- Testfällen für  P1 Buchung_aktuallisieren
--Buchung beenden Parameter: (ID Buchung, Aktion) *Stornieren ist noch nicht implementiert
SELECT * FROM buchung order by buchung_id desc;
EXECUTE Buchung_aktuallisieren(1, 'BEENDET' );
execute Fahrzeuge_nach_Status('RESERVIERT');
execute Fahrzeuge_nach_Status('VERFUEGBAR');
EXECUTE Buchung_aktuallisieren(2, 'BEENDET');
EXECUTE Buchung_aktuallisieren(3, 'BEENDET');
EXECUTE Buchung_aktuallisieren(6, 'BEENDET');
SELECT * FROM buchung order by buchung_id desc;

-- Testfällen für P3 P_Rechnung_Summe
-- zeige mir bestimmte Rechnungen Küftig wird es auch möglich sein Rechnungen eines bestimmten Nutzer anzeigen.
SELECT * FROM rechnung order by rechnung_id desc;
EXECUTE P_Rechnung_Summe('OFFEN');
SELECT * FROM rechnung order by rechnung_id desc;
update rechnung set rechnung_status='BEZAHLT' where rechnung_id=1;
EXECUTE P_Rechnung_Summe('BEZAHLT');
update rechnung set rechnung_status='BEZAHLT' where rechnung_id=2;
update rechnung set rechnung_status='BEZAHLT' where rechnung_id=3;
EXECUTE P_Rechnung_Summe('OFFEN');
/*-- T1 wird verhindern, dass ein Nutzer eine Buchung durchführt, 
wenn seine gesamte Schulden(offene Rechnungen) über x-EUR liegen. zB. 100EUR
*/

-- Test für T2 Eingabe des Passworts Trigger
--Passwor zu kurz
INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT) VALUES('KUNDE_UNMOEGLICH@mail.mx','1234567a');   
--Passwor ohne Zahlen
INSERT INTO Nutzerkonto(E_MAIL_ADRESSE, PASSWORT) VALUES('KUNDE_UNMOEGLICH@mail.mx','JustChars');  
SELECT * FROM nutzerkonto;
SELECT * FROM AUTOMATISCHE_ENTSCHL�SSELUNG;


/*-- T3 wird ein Saldo vergebenzB. 10EUR für alle Nutzer die innerhalbt eines Zeitraumen(eine bestimmte Woche),
mehr als Y Buchungen abgeschlossen und direrkt bezahlt haben.
*/
SELECT * FROM nutzer order by nutzer_id asc;
SELECT * FROM buchung order by buchung_id asc;

--Wie viele Buchungen f�r einen Nutzer
SELECT  b.buchung_bewertung, b.buchung_id FROM buchung b where b.fahrer_id=3;
-- Bewertung fuer die Buchungen
update buchung set buchung_bewertung=1 where buchung_id=1;
update buchung set buchung_bewertung=1 where buchung_id=2;
update buchung set buchung_bewertung=1 where buchung_id=3;
SELECT * FROM nutzer order by nutzer_id asc;
update buchung set buchung_bewertung=3 where buchung_id=4;

--EXECUTE Buchungen_eines_Nutzers(3);


