Matrikkel API
=============

MatrikkelAPI option (The SOAP-API)
----------------------
https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/dokumentasjon/index.html

Local DB option
---------------
A second solution using a local database import has been added.
The URL for downloading the addreses is stored in AddressImportService::ADDRESS_URL

A database table names matrikkel_addresses must be created in the default database:

```
CREATE TABLE `matrikkel_adresser` (
  `adresseId` bigint(11) UNSIGNED NOT NULL,
  `fylkesnummer` tinyint(2) UNSIGNED NOT NULL,
  `kommunenummer` smallint(11) UNSIGNED NOT NULL,
  `kommunenavn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `adressetype` varchar(255) NOT NULL,
  `adressekode` mediumint(6) UNSIGNED NOT NULL,
  `adressenavn` varchar(255) NOT NULL,
  `nummer` smallint(6) NOT NULL,
  `bokstav` varchar(2) NOT NULL,
  `gardsnummer` smallint(6) UNSIGNED NOT NULL,
  `bruksnummer` smallint(6) UNSIGNED NOT NULL,
  `festenummer` smallint(6) UNSIGNED DEFAULT NULL,
  `seksjonsnummer` smallint(6) UNSIGNED DEFAULT NULL,
  `undernummer` smallint(6) UNSIGNED DEFAULT NULL,
  `adresseTekst` varchar(255) NOT NULL,
  `epsg` smallint(6) UNSIGNED NOT NULL,
  `nord` float NOT NULL,
  `øst` float NOT NULL,
  `postnummer` smallint(6) UNSIGNED NOT NULL,
  `poststed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_danish_ci NOT NULL,
  `grunnkretsnavn` varchar(255) NOT NULL,
  `soknenavn` varchar(255) NOT NULL,
  `tettstednavn` varchar(255) NOT NULL,
  `search_context` varchar(512) DEFAULT '',
  `timestamp_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `matrikkel_adresser`
  ADD PRIMARY KEY (`adresseId`),
  ADD KEY `fylkesnummer` (`fylkesnummer`),
  ADD KEY `adressenavn` (`adressenavn`),
  ADD KEY `postnummer` (`postnummer`),
  ADD KEY `search_context` (`search_context`);

CREATE TABLE `matrikkel_bruksenheter` (
  `adresseId` bigint(11) NOT NULL,
  `bruksenhet` varchar(5) NOT NULL,
  `timestamp_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `matrikkel_bruksenheter`
  ADD PRIMARY KEY (`adresseId`,`bruksenhet`);

COMMIT;
```

Run console command matrikkel:adresse-import to import the addresses (about 2,5 million)
This command should be run at regular intervals