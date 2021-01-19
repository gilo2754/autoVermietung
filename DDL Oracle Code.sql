--`Letzte Aktuallisierung 20.5.2020`

--DROP SEQUENCE
Drop SEQUENCE SEQ_NUT_ID;
Drop SEQUENCE SEQ_FZG_ID;
Drop SEQUENCE SEQ_KKT_ID;
Drop SEQUENCE SEQ_BCG_ID;
Drop SEQUENCE SEQ_VRS_ID;
Drop SEQUENCE SEQ_ZLG_ID;
Drop SEQUENCE SEQ_SDN_ID;
DROP SEQUENCE SEQ_RCH_ID;
DROP SEQUENCE SEQ_FAR_ID;

--DROP TYPES
DROP TYPE Nutzer_t FORCE;
DROP TYPE Zahlung_t FORCE;
DROP TYPE Anschrift_t FORCE;

--DROP TABLE
DROP TABLE Nutzerkonto CASCADE CONSTRAINTS;
Drop TABLE Fuehrerschein CASCADE CONSTRAINTS;
Drop TABLE KFZ_Versicherung CASCADE CONSTRAINTS;
Drop TABLE Zahlung_Methode CASCADE CONSTRAINTS;
DROP TABLE Schaden CASCADE CONSTRAINTS;
DROP TABLE Buchung CASCADE CONSTRAINTS;
Drop TABLE Nutzer CASCADE CONSTRAINTS;
DROP TABLE FAHRZEUG CASCADE CONSTRAINTS;
DROP TABLE Rechnung CASCADE CONSTRAINTS;
DROP TABLE Fahrzeughalter CASCADE CONSTRAINTS;
DROP TABLE Fahrer CASCADE CONSTRAINTS;
DROP TABLE Paypal CASCADE CONSTRAINTS;
DROP TABLE Lastschrift Cascade Constraints;


--Nutzer_ID
CREATE SEQUENCE SEQ_NUT_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Fahrzeug_ID
CREATE SEQUENCE SEQ_FZG_ID --B
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Kundenkonto
CREATE SEQUENCE SEQ_KKT_ID --B
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Buchung
CREATE SEQUENCE SEQ_BCG_ID --B
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Versicherung
CREATE SEQUENCE SEQ_VRS_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Zahlung
CREATE SEQUENCE SEQ_ZLG_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Schaden
CREATE SEQUENCE SEQ_SDN_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Rechnung
CREATE SEQUENCE SEQ_RCH_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

--Fahrer
CREATE SEQUENCE SEQ_FAR_ID
    INCREMENT BY 1
    START WITH 1
    NOMAXVALUE
    NOCYCLE
    CACHE 25;

CREATE TABLE Nutzerkonto(
                            Nutzerkonto_ID NUMBER DEFAULT SEQ_KKT_ID.NEXTVAL PRIMARY KEY NOT NULL,
                            E_Mail_Adresse VARCHAR2(250) NOT NULL,
                            Passwort VARCHAR2(250) NOT NULL
);

--Anschrift_Objekt
CREATE OR REPLACE TYPE Anschrift_t AS OBJECT
(                      Strasse VARCHAR2 (80), 
                       Hausnummer VARCHAR2 (5),
                       PLZ VARCHAR2 (5), 
                       Ort VARCHAR2 (80),
                       Land VARCHAR2 (80)
);
/

--Nutzer_Objekt
CREATE OR REPLACE TYPE Nutzer_t AS OBJECT
( Nutzer_ID NUMBER,
Nutzerkonto_ID NUMBER,
Nutzername VARCHAR2(250),
Vorname VARCHAR2(250),
Nachname VARCHAR2(250),
Mobilnummer VARCHAR2(250),
Nutzer_Status VARCHAR2(250),
Nutzer_Bewertung FLOAT,
Saldo FLOAT,
Anschrift Anschrift_t
--FOREIGN KEY (Nutzerkonto_ID) REFERENCES Nutzerkonto (Nutzerkonto_ID)
--FOREIGN KEY (KFZ_Versicherung_ID) REFERENCES KFZ_Versicherung (KFZ_Versicherung_ID)
);
/

CREATE TABLE Nutzer OF Nutzer_t(
Nutzer_ID DEFAULT SEQ_NUT_ID.NEXTVAL PRIMARY KEY NOT NULL,
Nutzerkonto_ID NOT NULL,
Nutzername NULL,
Vorname NULL,
Nachname NULL,
Mobilnummer NULL,
Nutzer_Status Null,
Nutzer_Bewertung null,
Saldo null,
FOREIGN KEY (Nutzerkonto_ID) REFERENCES Nutzerkonto (Nutzerkonto_ID)
);
/

-- Fahrzeughalter
CREATE TABLE Fahrzeughalter(
                        Nutzer_id NUMBER DEFAULT SEQ_NUT_ID.nextval NOT NULL PRIMARY KEY,
                        Nutzer Nutzer_t,
                        Gewinn FLOAT,
                     FOREIGN KEY (Nutzer_id) REFERENCES Nutzer (Nutzer_ID)
);

--Fahrer
CREATE TABLE Fahrer(
                      Nutzer_ID NUMBER DEFAULT SEQ_FAR_ID.nextval NOT NULL PRIMARY KEY,
                      Nutzer Nutzer_t,
                      Saldo FLOAT,
                      FOREIGN KEY (Nutzer_ID) REFERENCES Nutzer (Nutzer_ID)
);


--Führerschein
CREATE TABLE Fuehrerschein(
                              Fuehrerschein_id NUMBER NOT NULL PRIMARY KEY,
                              Fuehrerschein_Nummer VARCHAR2(11),
                              Nutzer_id NUMBER NOT NULL,
                              Ablauf_datum DATE,
                              Fahrzeug_klasse	VARCHAR2(250),
                              FOREIGN KEY (Nutzer_id) REFERENCES Nutzer (Nutzer_ID)
);


--Fahrzeug
CREATE TABLE Fahrzeug (
                          Fahrzeug_ID NUMBER DEFAULT SEQ_FZG_ID.NEXTVAL PRIMARY KEY NOT NULL,
                          Fahrzeugkennzeichen varchar2(10) NOT NULL,
                          Fahrzeughalter_id NUMBER NOT NULL,
                          Fahrzeug_status VARCHAR2(250),
                          Fahrzeug_typ VARCHAR2(250),
                          Fahrzeug_marke VARCHAR2(250),
                          Fahrzeug_modell VARCHAR2(250),
                          Fahrzeug_Preis_Faktor FLOAT  NULL,
                          Herstellungsjahr NUMBER,
                          FOREIGN KEY (Fahrzeughalter_id) REFERENCES Nutzer (Nutzer_ID)
);


--Versicherung
CREATE TABLE KFZ_Versicherung(
                                 KFZ_Versicherung_ID NUMBER DEFAULT SEQ_VRS_ID.NEXTVAL PRIMARY KEY NOT NULL,
                                 Nutzer_ID NUMBER NOT NULL,
                                 Versicherrungstyp	VARCHAR2(250),
                                 Summe_Selbstbeteiligung	FLOAT	NOT NULL,
                                 Datum_Versicherungsbeginn	DATE	NOT NULL,
                                 Deckungssumme	DEC	NOT NULL,
                                 FOREIGN KEY (Nutzer_ID) REFERENCES Nutzer (Nutzer_ID)
);

--Schaden
CREATE TABLE Schaden (
                         Schaden_ID NUMBER DEFAULT SEQ_SDN_ID.NEXTVAL PRIMARY KEY NOT NULL,
                         Verurscher_id NUMBER NOT NULL,
                         Beschreibung VARCHAR2(250)	,
                         Umfallschaden_Summe	FLOAT	NOT NULL,
                         FOREIGN KEY (Verurscher_id) REFERENCES Nutzer (Nutzer_ID)ON DELETE SET NULL --da unsicher
);

--Buchung
CREATE TABLE Buchung (
                         Buchung_ID NUMBER DEFAULT SEQ_BCG_ID.NEXTVAL NOT NULL PRIMARY KEY,
                         Fahrer_id	NUMBER	NOT NULL,
                         Fahrzeug_id 	NUMBER	NOT NULL,
                         Buchung_Status	Varchar2(20),
                         Buchung_datum	DATE	NOT NULL,
                         Buchung_End_Preis	FLOAT,   --End_Preis wird durch Preis_Faktor von Fahrzeug berechnet
                         -- Buchung_Start_Puffer number null,
                         Buchung_Start Timestamp NOT NULL,
                         Buchung_Ende Timestamp  null,
                         Buchung_Dauer NUMBER  null,
                         Buchung_Bewertung float,
                         FOREIGN KEY (Fahrer_id) REFERENCES Nutzer (Nutzer_id),
--FOREIGN KEY (Fahrer_id) REFERENCES Fahrer (Nutzer_id),
                        FOREIGN KEY (Fahrzeug_id) REFERENCES Fahrzeug (Fahrzeug_ID)
);


--Zahlungs_Objekt
CREATE OR REPLACE TYPE Zahlung_t AS OBJECT
(                        Zahlung_M_ID NUMBER,
                         Zahlung_Datum	DATE,
                         Nutzer_id number   

);
/

--ZahlungsMethode_Tabelle
CREATE TABLE Zahlung_Methode OF Zahlung_t (
                         Zahlung_M_ID  DEFAULT SEQ_ZLG_ID.NEXTVAL PRIMARY KEY NOT NULL,
                         Zahlung_Datum		NOT NULL, -- wann die Zahlungsmethode hinzugefuegt wurde
                        FOREIGN KEY (Nutzer_id) REFERENCES Nutzer (Nutzer_id)

);
/

--Lastschrift
CREATE TABLE Lastschrift (
                             Zahlung Zahlung_t,
                             IBAN	VARCHAR2(250)	NOT NULL,
                             BIC	VARCHAR2(250)	NOT NULL,
                             Bankname	VARCHAR2(250)	NOT NULL
);

--Paypal
CREATE TABLE Paypal(
                       Zahlung Zahlung_t,
                       Paypal_Email VARCHAR2 (50) NOT NULL
);


--Rechnung
CREATE TABLE Rechnung (
                          Rechnung_ID NUMBER DEFAULT SEQ_RCH_ID.NEXTVAL NOT NULL PRIMARY KEY,
                          Buchung_ID	NUMBER	NOT NULL,
                          KFZ_Versicherung_id	NUMBER	 NULL,
                          Schaden_id 	NUMBER	 NULL,
                          Zahlung_M_ID 	NUMBER	 NULL,
                          Rechnung_Bezeichnung VARCHAR2(250),
                          Rechnung_Datum	DATE  NULL,
                          Rechnung_Status	VARCHAR2(250)	NOT NULL,
                          Endbetrag	FLOAT	 NULL,
                          FOREIGN KEY (Zahlung_M_ID) REFERENCES Zahlung_Methode (Zahlung_M_ID),
                          FOREIGN KEY (Buchung_ID) REFERENCES Buchung (Buchung_ID),
                          FOREIGN KEY (KFZ_Versicherung_id) REFERENCES KFZ_Versicherung (KFZ_Versicherung_ID),
                          FOREIGN KEY (Schaden_id) REFERENCES Schaden (Schaden_ID)
);

