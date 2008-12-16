

CREATE TABLE Dictionary (
  DicLabelID INTEGER UNSIGNED NOT NULL,
  LangID VARCHAR(10) NOT NULL,
  DicTranslation VARCHAR(30) NULL,
  DicTechHelp VARCHAR(50) NULL,
  DicBasDesc TEXT NULL,
  DicFullDesc TEXT NULL,
  PRIMARY KEY(DicLabelID, LangID)
);

CREATE TABLE LabelGroup (
  DicLabelID INTEGER UNSIGNED NOT NULL,
  LGName VARCHAR(50) NULL,
  LabelName VARCHAR(30) NULL,
  LGOrder INTEGER UNSIGNED NULL,
  PRIMARY KEY(DicLabelID)
);

CREATE TABLE Language (
  LangID VARCHAR(10) NOT NULL,
  LangName VARCHAR(20) NULL,
  LangNameEN VARCHAR(20) NULL,
  LangNotes TEXT NULL,
  LangAdmin VARCHAR(20) NULL,
  LangActive BOOL NULL,
  PRIMARY KEY(LangID)
);

insert into Language values ('es', 'Español', 'Spanish', 'Spain and LatinAmerica', 'wwwmngr@desinventar.org', 'false');
insert into Language values ('en', 'English', 'English', 'Universal..', 'wwwmngr@desinventar.org', 'false');
insert into Language values ('fr', 'Francois', 'French', 'France, Africa..', 'wwwmngr@desinventar.org', 'false');
insert into Language values ('pt', 'Portugues', 'Portuguese', 'Brazil, Portugal, Africa', 'wwwmngr@desinventar.org', 'false');
