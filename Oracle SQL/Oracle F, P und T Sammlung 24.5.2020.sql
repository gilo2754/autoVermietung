--************** F1 Tarif_berechnen**************
create or replace FUNCTION Tarif_berechnen (Zeit FLOAT,Faktor_Preis IN NUMBER)
RETURN FLOAT
IS
    Betrag FLOAT;
BEGIN

Betrag := CASE 
--Pro WHEN eine Berechnung und Faktor_Preis anpassen
    WHEN Zeit>=0 and Zeit <60 THEN Zeit*Faktor_Preis--'Pro Minute'
    WHEN Zeit>=60 and Zeit <240 THEN Zeit*Faktor_Preis*0.60 --'Pro Stunde bis 4 Stunden' 
    WHEN Zeit>=240 and Zeit <10080 THEN Zeit*0.50*Faktor_Preis--'Pro_Tag und bis 7 Tage'
    WHEN Zeit >=10080 THEN Zeit*0.35*Faktor_Preis--'ab 7 Tage'
    ELSE 0    --Betrag=0
END;
IF(Betrag>0)THEN
DBMS_OUTPUT.PUT_LINE ('Betrag zu bezahlen: '|| Betrag||' EUR');
ELSE
RAISE_APPLICATION_ERROR(-20008, 'Die Buchung ist k�rzer als eine Minute. Es kann kein Tarif berechnet werden!');
END  IF;
RETURN Betrag;
END;
/

--************** F2.1 Zeichenkette_Verschluesseln + F2.2 ZEICHENKETTE_ENTSCHL�SSELN **************
/* Mit der Funktion kann man Zeichenketten verschl�sseln, zB Passw�rter und Bankdaten, Funktion muss hierf�r in einem Trigger aufgerufen werden */ 

create or replace FUNCTION Zeichenkette_Verschl�sseln (f_Zeichenkette IN VARCHAR2) RETURN VARCHAR2
IS
v_Zeichenkette VARCHAR2(255) := f_Zeichenkette; 
v_verschl�ssel_Zeichenkette VARCHAR2(16); 
v_schl�ssel_encrypt VARCHAR2(16) := 'Hallo123Hallo456';
v_length_zeichenkette NUMBER := LENGTH(v_Zeichenkette);

BEGIN

/* Dies wird von dem Fehler "no data passed to obfuscation toolkit" selbst abgefangen, daher IF-Abfrage nicht zwingend notwendig */

IF v_length_zeichenkette < 1 
THEN 
    RAISE_APPLICATION_ERROR(-20005, 'Die Zeichenkette muss mindestens ein Zeichen enthalten!');
END IF;

IF REGEXP_LIKE(v_zeichenkette, '\s') --regular Expression f�r Whitespaces/Leerzeichen 
THEN
    RAISE_APPLICATION_ERROR(-20004, 'Eingegebene Zeichenkette darf keine Leerzeichen enthalten!');
END IF;

v_Zeichenkette := RPAD( v_Zeichenkette, (TRUNC(LENGTH(v_Zeichenkette)/8+0.9)*8), CHR(0)); --Zahl muss durch 8 teilbar sein
/* TRUNC beschneidet die Nachkommastellen einer Rechnung: L�nge des Passwortes / 8 (durchschnittliche L�nge) und multipliziert 
wieder mit 8; RPAD Padding rechts: Elemente anf�gen mit CHR(0), Char mit Nullen erweitern */
v_verschl�ssel_Zeichenkette := dbms_obfuscation_toolkit.DES3Encrypt (
    -- Package zum Verschl�sseln, Funktion des3encrypt zum Verschl�sseln (3 = tripple encryption, desencrypt wurde schon gehackt)
    input_string => v_Zeichenkette,
    which => 0,
    key_string => v_schl�ssel_encrypt );
    -- DES3Encrypt ben�tigt einen Input_string, einen Schl�ssel zum Entschl�sseln (Hallo123), which => 1 (192 Bit Key) => 0 (128 Bit Key)       
RETURN v_verschl�ssel_Zeichenkette; -- R�ckgabe des verschl�sselten Passwortes, der die Funktion DES3Encrypt zugewiesen wurde
END Zeichenkette_Verschl�sseln;
/


create or replace FUNCTION ZEICHENKETTE_ENTSCHL�SSELN (f_Zeichenkette IN VARCHAR2) RETURN VARCHAR2
IS
v_Zeichenkette VARCHAR2(255) := f_Zeichenkette; 
v_entschl�ssel_Zeichenkette VARCHAR2(16); 
v_schl�ssel_decrypt VARCHAR2(16) := 'Hallo123Hallo456';

BEGIN
v_entschl�ssel_Zeichenkette := dbms_obfuscation_toolkit.DES3Decrypt (
    -- Package zum Ver-/Entschl�sseln, Funktion des3decrypt zum Entschl�sseln (3 = tripple encryption, desencrypt wurde schon gehackt)
    input_string => v_Zeichenkette,
    which => 0,
    key_string => v_schl�ssel_decrypt );
    -- DES3Encrypt ben�tigt einen Input_string, einen Schl�ssel zum Entschl�sseln (Hallo123), which => 1 (192 Bit Key) => 0 (128 Bit Key)       
RETURN v_entschl�ssel_Zeichenkette; -- R�ckgabe des verschl�sselten Passwortes, der die Funktion DES3Encrypt zugewiesen wurde
END ZEICHENKETTE_ENTSCHL�SSELN;
/

/* VIEW zur automatischen Entschl�sselung, wo Funktion Zeichenkette_entschl�sseln aufgerufen wird. Das geht auch wenn man in die View zus�tzlich von Tabelle Lastschrift die IBAN ,BIC und Bankname packt (zb Zeichenkette_entschl�sseln(IBAN) as IBAN ect.), allerdings fehlten mir dazu Testdaten.) */

-- *******P1 Buchung_aktuallisieren (momentan nur Buchung beenden, nachher auch stornieren)*********
create or replace PROCEDURE Buchung_aktuallisieren(p_Buchung_ID NUMBER, p_Aktion VARCHAR2)
IS  
v_B_Status VARCHAR2(255);
v_Dauer number; --Zwischenspeicher für Buchung_Dauer
v_Tarif float;
v_Preis_Faktor float;
v_Fahrzeug_ID number;
v_Buchung_ID number;
v_Fahrer_ID number;

BEGIN
 select buchung_status into v_B_Status from buchung where Buchung_ID=p_Buchung_ID;

    IF (p_Aktion='BEENDET' and v_B_Status='LAUFEND')THEN --WENN B_Status ist gerade LAUFEND, kann zu BEENDET geändert werden 
    update Buchung SET Buchung_status='BEENDET' WHERE Buchung_ID=p_Buchung_ID; --aktuallisiere Buchung_Status
    update Buchung SET Buchung_ende=sysdate WHERE Buchung_ID=p_Buchung_ID;

    SELECT ( CAST( Buchung_ende AS DATE ) - CAST( buchung_start AS DATE ) ) * 1440 into v_Dauer   from buchung where buchung_id=p_Buchung_ID ;
    SELECT ROUND(v_Dauer)into v_Dauer from dual;
    update Buchung SET Buchung_dauer=v_Dauer WHERE Buchung_ID = p_Buchung_ID;

       DBMS_OUTPUT.PUT_LINE ('Buchung '|| p_Buchung_ID ||' hat das Status ' ||p_Aktion||' und hat ' ||v_Dauer||' Minuten gedauert.');
        select Fahrzeug_id into v_Fahrzeug_ID from Buchung where Buchung_ID=p_Buchung_ID  ; --Fahrzeug_id's von Fahrzeug(in Tab fahrzeug) und die aktuelle Buchung
        SELECT Fahrzeug_Preis_Faktor into v_Preis_Faktor   from Fahrzeug where Fahrzeug_ID =v_Fahrzeug_ID;
        SELECT Tarif_berechnen(v_Dauer,v_Preis_Faktor) into v_Tarif FROM DUAL;
        update Fahrzeug set Fahrzeug_Status='VERFUEGBAR' where Fahrzeug_ID=v_Fahrzeug_ID; --Fahrzeug ist wieder VERFUEGBAR

--Rechnung erstellen: (SOLLTEN WIR EINE FUNKTION/PROD DAFÜR MACHEN?)
        select Buchung_ID into v_Buchung_ID from Buchung where Buchung_ID=p_Buchung_ID  ;
        insert into Rechnung(Buchung_ID, Rechnung_Bezeichnung, Rechnung_Datum, Rechnung_Status, Endbetrag) 
        values(v_Buchung_ID, 'Buchung', sysdate, 'OFFEN', v_Tarif);
        update buchung set buchung_end_preis=v_Tarif where buchung_ID=p_Buchung_ID; --Buchung_End_Preis=V_Tarif

--aktualisiere das Saldo dieses Nutzers       
        select Fahrer_ID into v_Fahrer_ID from Buchung where buchung_ID=p_Buchung_ID;
        update nutzer set saldo=v_Tarif where nutzer_ID=v_Fahrer_ID; --Buchung_End_Preis=V_Tarif

    END IF;
END;
/

--************** P2 Fahrzeug_buchen_P**************
create or replace PROCEDURE Fahrzeug_buchen_P (p_Fahrer_ID  number, p_Fahrzeug_ID  NUMBER)
IS
    v_Fahrzeug_Status VARCHAR2(255);
    v_Nutzer_Status VARCHAR2(255);
    v_msg VARCHAR2(255);
    
BEGIN
    SELECT Fahrzeug_Status into v_Fahrzeug_Status   from Fahrzeug where Fahrzeug_ID =p_Fahrzeug_ID;
    SELECT Nutzer_Status into v_Nutzer_Status   from Nutzer where Nutzer_ID =p_Fahrer_ID;

IF (v_Fahrzeug_Status='VERFUEGBAR' and v_Nutzer_Status='AKTIV' ) THEN
            INSERT INTO Buchung(FAHRER_ID, FAHRZEUG_ID, BUCHUNG_DATUM,BUCHUNG_START, Buchung_status) 
            VALUES(p_Fahrer_ID, p_Fahrzeug_ID, SYSDATE, SYSDATE, 'LAUFEND'); 

            update Fahrzeug set Fahrzeug_Status='RESERVIERT' where Fahrzeug_ID=p_Fahrzeug_ID;
            v_msg:='Ihre Buchung laeuft schon :D';
            select Fahrzeug_Status into v_Fahrzeug_Status from fahrzeug where Fahrzeug_ID=p_Fahrzeug_ID;
            DBMS_OUTPUT.PUT_LINE ('Neuer Status des Fahrzeugs: '||p_Fahrzeug_ID|| ' ist '|| v_Fahrzeug_Status);
            DBMS_OUTPUT.PUT_LINE(v_msg);

   ELSIF (v_Fahrzeug_Status ='RESERVIERT') THEN 
            v_msg := ('Buchung nicht moeglich fÃ¼r das Fahrzeug '|| p_Fahrzeug_ID); --wie zB. :
            DBMS_OUTPUT.PUT_LINE (v_msg);
            DBMS_OUTPUT.PUT_LINE ('Versuchen Sie mit einem verfuegbaren Fahrzeug');
            Fahrzeuge_nach_Status('VERFUEGBAR'); --P4 zeige Fahrzeg nach Status an
     ELSIF (v_Nutzer_Status='GESPERRT') THEN 
            DBMS_OUTPUT.PUT_LINE ('Ihr Konto ist zurzeit gesperrt. Eine Buchung ist fuer Sie leider nicht moeglich.');

    ELSE
         v_msg := ('Option ungueltig');   
END IF;    
END;

/

----************** P3 P_Rechnung_Summe --**************
create or replace PROCEDURE Rechnung_Summe(p_Rechnung_Status VARCHAR2)--, p_Nutzer_ID IN NUMBER) --damit bestimmte Rechnungn für einen bestimmten Kunde gesucht werden können
    -- RETURN FLOAT
    IS
    v_Rechnung_Summe FLOAT:=0;
    v_Rechnung_Summe_tmp FLOAT:=0;
    v_Rechnung_ID number;
    v_R_Bezeichnung VARCHAR2(255);
    v_R_Status VARCHAR2(255);

    CURSOR rechnung_cur IS
        SELECT rechnung_id,  Rechnung_Bezeichnung, Rechnung_Status, Endbetrag 
        FROM Rechnung where Rechnung_Status=p_Rechnung_Status ORDER BY Rechnung_id ASC; --Just same status mit "Rechnung_Status=p_Rechnung_Status"
        BEGIN 
        OPEN rechnung_cur;   
        FETCH rechnung_cur INTO v_Rechnung_ID,  v_R_Bezeichnung, v_R_Status, v_Rechnung_Summe_tmp;

        WHILE (rechnung_cur%FOUND)
        LOOP      
            --exit when rechnung_cur%notFOUND;
            DBMS_OUTPUT.PUT_LINE('ID der Rechnung: ' ||v_Rechnung_ID||' Bezeichnung: '||v_R_Bezeichnung|| ' Status: '||v_R_Status|| ' EndBetrag: '||v_Rechnung_Summe_tmp);
            v_Rechnung_Summe:=v_Rechnung_Summe+v_Rechnung_Summe_tmp; -- Betraege akkumuliere
            FETCH rechnung_cur INTO v_Rechnung_ID,  v_R_Bezeichnung, v_R_Status, v_Rechnung_Summe_tmp;

        END LOOP;
        CLOSE rechnung_cur;
                DBMS_OUTPUT.PUT_LINE('Summe der Rechnungen mit Status: '||p_Rechnung_Status||': '|| v_Rechnung_Summe);
    --   RETURN v_Rechnung_Summe; --Summe der ausgewählten Rechnungen TODO
        END;
/


--************** P4 Fahrzeuge_nach_Status**************
--Parameter(#gesuchter_Status[RESERVIERT/VERFUEGBAR/INAKTIV]])

create or replace PROCEDURE Fahrzeuge_nach_Status(p_F_Status VARCHAR2)
IS
 -- v_F_Status VARCHAR2(255);  
  v_fahrzeug_ID NUMBER;
  v_fahrzeug_marke varchar2(255);
        
      -- DBMS_OUTPUT.PUT_LINE('Folgende Fahrzeuge haben den Status ');--|| p_F_Status||' ');
  CURSOR fahrzeug_cur IS
     SELECT fahrzeug_id, fahrzeug_marke FROM fahrzeug where fahrzeug_status=p_F_Status ORDER BY fahrzeug_id ASC;
 

BEGIN
    OPEN fahrzeug_cur;
    FETCH fahrzeug_cur INTO v_fahrzeug_ID,  v_fahrzeug_marke;
    WHILE fahrzeug_cur%FOUND
    
    LOOP DBMS_OUTPUT.PUT_LINE('ID des Fahrzeugs: ' ||v_fahrzeug_ID||' Marke: '||v_fahrzeug_marke|| ' '||p_F_Status);
    FETCH fahrzeug_cur INTO v_fahrzeug_ID,  v_fahrzeug_marke;
    END LOOP;
    CLOSE fahrzeug_cur;

    END;
/


--T1 Nutzerbewertung anhand Buchungbewertung aktuallisieren und Nutzer sperren 
CREATE OR REPLACE TRIGGER T1_Nutzer_Buchung_Bewertung
        FOR UPDATE   OF buchung_bewertung ON Buchung
        COMPOUND TRIGGER

v_Bewertung number;
v_buchung_id number; 
v_fahrer_id number;
v_Bewertung_der_Buchung FLOAT;
v_Bewertung_eines_Nutzers FLOAT;
v_Nutzer_Status varchar2(255);

CURSOR Buchung_cur IS
        SELECT buchung_id,  fahrer_id, Buchung_Bewertung, Nutzer_Status 
        FROM buchung, nutzer where buchung.fahrer_id=nutzer_id  ORDER BY buchung_id ASC; -- unsicher

-- gefeuert pro Zeile /Fila
AFTER EACH ROW IS
    BEGIN
        dbms_output.put_line('Eine Bewertung wurde hinzugefügt');
END AFTER EACH ROW;

-- gefeuert pro statement
AFTER STATEMENT IS
    BEGIN  
       SELECT count(*)
         INTO v_Bewertung
         FROM buchung, nutzer
        WHERE nutzer_id =buchung.fahrer_id;
 OPEN Buchung_cur;   
     FETCH Buchung_cur INTO v_buchung_id,  v_fahrer_id, v_Bewertung_der_Buchung, v_Nutzer_Status;

        WHILE (Buchung_cur%FOUND)
        LOOP      
            DBMS_OUTPUT.PUT_LINE('Bewertung der Buchung '||v_buchung_id|| ' ist: ' ||v_Bewertung_der_Buchung  || '. Fahrer: ' ||  v_fahrer_id);
--Ueberpruefung, dass die Buchungsbewertung richtig angegeben wird          
                            select Nutzer_Bewertung into v_Bewertung_der_Buchung from nutzer  where Nutzer_id=v_fahrer_id;
  
          if(v_Bewertung_der_Buchung > 5 and v_Bewertung_der_Buchung <=0.1)then 
                    DBMS_OUTPUT.PUT_LINE('Die Buchungsbewertung muss zwischen 0,1 und 5,0 liegen!');
          end if;
          
          if(v_Bewertung_der_Buchung <= 5 and v_Bewertung_der_Buchung >0.1)then 
                    update nutzer SET Nutzer_Bewertung=ROUND((Nutzer_Bewertung+v_Bewertung_der_Buchung)/2,1) WHERE Nutzer_id=v_fahrer_id;
          end if;
          
            
            if(v_Bewertung_der_Buchung<1.5 and v_Nutzer_Status='AKTIV') then
                     update nutzer SET Nutzer_status='GESPERRT' WHERE Nutzer_Bewertung<1.5 ;
                     DBMS_OUTPUT.PUT_LINE('Fahrer '||v_fahrer_id|| ' wurde wegen nidrieger Bewertung gesperrt. Letzte Bewertung:' ||v_Bewertung_der_Buchung);
              end if;
               FETCH Buchung_cur INTO v_buchung_id,  v_fahrer_id, v_Bewertung_der_Buchung, v_Nutzer_Status;
        END LOOP;
        CLOSE Buchung_cur;
        DBMS_OUTPUT.PUT_LINE('Anzahl der Bewertungen: ' || v_Bewertung);
END AFTER STATEMENT;
END T1_Nutzer_Buchung_Bewertung;
/


-- T2.1 Passwort_Eingabe 
-- HIER wurde die Funktion 2 genutzt für die verschlüsserung der Passwörten

CREATE OR REPLACE TRIGGER Passwort_Eingabe
BEFORE INSERT OR UPDATE OF PASSWORT ON NUTZERKONTO 
FOR EACH ROW 
DECLARE
v_passwort_length Number := LENGTH(:NEW.passwort);
BEGIN 

IF 7 >= v_passwort_length
THEN 
    RAISE_APPLICATION_ERROR(-20002, 'Eingegebenes Passwort ist zu kurz!');
END IF;

IF NOT REGEXP_LIKE(:NEW.passwort,'[^\w\s]') --Regular Expression, no words, no whitespaces
THEN 
    RAISE_APPLICATION_ERROR(-20003, 'Eingegebenes Passwort muss eines der Sonderzeichen enthalten!');
END IF;

IF NOT REGEXP_LIKE(:NEW.passwort, '[A-Z]') 
THEN
    RAISE_APPLICATION_ERROR(-20004, 'Eingegebenes Passwort muss einen Gro�buchstaben enthalten!');
END IF;

IF NOT REGEXP_LIKE(:NEW.passwort, '\d') 
THEN
    RAISE_APPLICATION_ERROR(-20005, 'Eingegebenes Passwort muss eine Zahl enthalten!');
END IF;

:NEW.passwort := Zeichenkette_Verschl�sseln(:NEW.passwort); 
-- Das Passwort wird vor dem Insert als verschl�sseltes Passwort gespeichert
DBMS_OUTPUT.PUT_LINE('Passwort wurde verschl�sselt eingef�gt!');

END;

/
-- T2.2 Lastschrift_Eingabe
CREATE OR REPLACE TRIGGER Lastschrift_Daten_Eingabe
BEFORE INSERT OR UPDATE ON Lastschrift
FOR EACH ROW 
DECLARE
v_iban_length Number := LENGTH(:NEW.IBAN);
v_bic_length Number := LENGTH(:NEW.BIC);

BEGIN 

--IBAN
IF 34 < v_iban_length
THEN 
    RAISE_APPLICATION_ERROR(-20002, 'Die eingegebene IBAN darf aus maximal 34 Zeichen bestehen!');
END IF;

IF NOT REGEXP_LIKE(:NEW.IBAN, '[A-Z]') 
THEN
    RAISE_APPLICATION_ERROR(-20004, 'Eingegebene IBAN muss mindestens einen Gro�buchstaben (L�ndercode) enthalten!');
END IF;

:NEW.IBAN := Zeichenkette_Verschl�sseln(:NEW.IBAN); 
-- Die IBAN wird vor dem Insert als verschl�sselte IBAN gespeichert
DBMS_OUTPUT.PUT_LINE('IBAN wurde verschl�sselt eingef�gt!');

--BIC
IF 11 < v_bic_length
THEN 
    RAISE_APPLICATION_ERROR(-20002, 'Die eingegebene BIC darf aus maximal 11 Zeichen bestehen!');
END IF;

IF NOT REGEXP_LIKE(:NEW.BIC, '[A-Z]') 
THEN
    RAISE_APPLICATION_ERROR(-20004, 'Eingegebene BIC muss mindestens einen Gro�buchstaben enthalten!');
END IF;

:NEW.BIC := Zeichenkette_Verschl�sseln(:NEW.BIC); 
-- Die BIC wird vor dem Insert als verschl�sselte BIC gespeichert
DBMS_OUTPUT.PUT_LINE('BIC wurde verschl�sselt eingef�gt!');

END;
/
