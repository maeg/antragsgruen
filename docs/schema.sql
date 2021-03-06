SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `antragsgruen` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `antragsgruen` ;

-- -----------------------------------------------------
-- Table `antragsgruen`.`veranstaltung`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`veranstaltung` (
  `id` SMALLINT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(200) NOT NULL ,
  `name_kurz` VARCHAR(45) NOT NULL ,
  `antrag_einleitung` TEXT NOT NULL ,
  `datum_von` DATE NULL ,
  `datum_bis` DATE NULL ,
  `antragsschluss` TIMESTAMP NULL DEFAULT NULL ,
  `policy_antraege` VARCHAR(20) NULL ,
  `policy_aenderungsantraege` VARCHAR(20) NULL ,
  `policy_kommentare` VARCHAR(20) NULL ,
  `policy_unterstuetzen` VARCHAR(20) NULL ,
  `typ` TINYINT NULL ,
  `yii_url` VARCHAR(45) NULL ,
  `admin_email` VARCHAR(150) NULL ,
  `freischaltung_antraege` TINYINT NOT NULL DEFAULT 1 ,
  `freischaltung_aenderungsantraege` TINYINT NOT NULL DEFAULT 1 ,
  `freischaltung_kommentare` TINYINT NOT NULL DEFAULT 0 ,
  `logo_url` VARCHAR(200) NULL ,
  `fb_logo_url` VARCHAR(200) NULL ,
  `ae_nummerierung_global` TINYINT NOT NULL DEFAULT 0 ,
  `zeilen_nummerierung_global` TINYINT NOT NULL DEFAULT 0 ,
  `bestaetigungs_emails` TINYINT NOT NULL DEFAULT 0 ,
  `revision_name_verstecken` TINYINT NOT NULL DEFAULT 0 ,
  `kommentare_unterstuetzbar` TINYINT NOT NULL DEFAULT 0 ,
  `ansicht_minimalistisch` TINYINT NOT NULL DEFAULT 0 ,
  `einstellungen` MEDIUMBLOB NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `yii_url_UNIQUE` (`yii_url` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`antrag`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`antrag` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `veranstaltung` SMALLINT NOT NULL ,
  `abgeleitet_von` INT NULL ,
  `typ` TINYINT NULL ,
  `name` TINYTEXT NOT NULL ,
  `revision_name` VARCHAR(50) NOT NULL ,
  `datum_einreichung` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `datum_beschluss` VARCHAR(45) NULL ,
  `text` MEDIUMTEXT NULL ,
  `begruendung` MEDIUMTEXT NULL ,
  `status` TINYINT NOT NULL ,
  `status_string` VARCHAR(55) NULL ,
  `cache_anzahl_zeilen` MEDIUMINT UNSIGNED NOT NULL ,
  `cache_anzahl_absaetze` MEDIUMINT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `veranstaltung` (`veranstaltung` ASC) ,
  INDEX `abgeleitet_von` (`abgeleitet_von` ASC) ,
  CONSTRAINT `fk_antrag_veranstaltung`
    FOREIGN KEY (`veranstaltung` )
    REFERENCES `antragsgruen`.`veranstaltung` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_antrag_antrag1`
    FOREIGN KEY (`abgeleitet_von` )
    REFERENCES `antragsgruen`.`antrag` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`person`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`person` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `typ` ENUM('person', 'organisation') NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `email` VARCHAR(200) NULL ,
  `telefon` VARCHAR(100) NULL ,
  `auth` VARCHAR(200) NULL ,
  `angelegt_datum` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `admin` TINYINT NOT NULL ,
  `status` TINYINT NOT NULL ,
  `pwd_enc` VARCHAR(100) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `auth_UNIQUE` (`auth` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`antrag_unterstuetzer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`antrag_unterstuetzer` (
  `antrag_id` INT NOT NULL ,
  `unterstuetzer_id` INT NOT NULL ,
  `rolle` ENUM('initiator', 'unterstuetzt', 'mag', 'magnicht') NOT NULL ,
  `kommentar` TEXT NULL ,
  `position` SMALLINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`antrag_id`, `unterstuetzer_id`, `rolle`) ,
  INDEX `fk_unterstuetzer_idx` (`unterstuetzer_id` ASC) ,
  INDEX `fk_antrag_idx` (`antrag_id` ASC) ,
  CONSTRAINT `fk_antrag`
    FOREIGN KEY (`antrag_id` )
    REFERENCES `antragsgruen`.`antrag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_unterstuetzer`
    FOREIGN KEY (`unterstuetzer_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`aenderungsantrag`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`aenderungsantrag` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `antrag_id` INT NULL ,
  `revision_name` VARCHAR(45) NULL ,
  `name_neu` TINYTEXT NULL ,
  `text_neu` MEDIUMTEXT NOT NULL ,
  `begruendung_neu` MEDIUMTEXT NOT NULL ,
  `aenderung_text` MEDIUMTEXT NOT NULL ,
  `aenderung_begruendung` MEDIUMTEXT NOT NULL ,
  `datum_einreichung` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `datum_beschluss` TIMESTAMP NULL ,
  `status` TINYINT NOT NULL ,
  `status_string` VARCHAR(55) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_aenderungsantrag_antrag1_idx` (`antrag_id` ASC) ,
  CONSTRAINT `fk_aenderungsantrag_antrag1`
    FOREIGN KEY (`antrag_id` )
    REFERENCES `antragsgruen`.`antrag` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`aenderungsantrag_unterstuetzer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`aenderungsantrag_unterstuetzer` (
  `aenderungsantrag_id` INT NOT NULL ,
  `unterstuetzer_id` INT NOT NULL ,
  `rolle` ENUM('initiator', 'unterstuetzt', 'mag', 'magnicht') NOT NULL ,
  `kommentar` TEXT NULL ,
  `position` SMALLINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`aenderungsantrag_id`, `unterstuetzer_id`, `rolle`) ,
  INDEX `fk_person_has_aenderungsantrag_aenderungsantrag1_idx` (`aenderungsantrag_id` ASC) ,
  INDEX `fk_person_has_aenderungsantrag_unterstuetzers1_idx` (`unterstuetzer_id` ASC) ,
  CONSTRAINT `fk_person_has_aenderungsantrag_unterstuetzers1`
    FOREIGN KEY (`unterstuetzer_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_has_aenderungsantrag_aenderungsantrag1`
    FOREIGN KEY (`aenderungsantrag_id` )
    REFERENCES `antragsgruen`.`aenderungsantrag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`antrag_kommentar`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`antrag_kommentar` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `verfasser_id` INT NULL ,
  `antrag_id` INT NULL ,
  `absatz` SMALLINT NULL ,
  `text` TEXT NOT NULL ,
  `datum` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `status` TINYINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_antrag_kommentar_person1_idx` (`verfasser_id` ASC) ,
  INDEX `fk_antrag_kommentar_antrag1_idx` (`antrag_id` ASC) ,
  CONSTRAINT `fk_antrag_kommentar_person1`
    FOREIGN KEY (`verfasser_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_antrag_kommentar_antrag1`
    FOREIGN KEY (`antrag_id` )
    REFERENCES `antragsgruen`.`antrag` (`id` )
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`aenderungsantrag_kommentar`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`aenderungsantrag_kommentar` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `verfasser_id` INT NULL ,
  `aenderungsantrag_id` INT NULL ,
  `absatz` SMALLINT NULL ,
  `text` TEXT NULL ,
  `datum` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ,
  `status` TINYINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_aenderungsantrag_kommentar_person1_idx` (`verfasser_id` ASC) ,
  INDEX `fk_aenderungsantrag_kommentar_aenderungsantrag1_idx` (`aenderungsantrag_id` ASC) ,
  CONSTRAINT `fk_aenderungsantrag_kommentar_person1`
    FOREIGN KEY (`verfasser_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_aenderungsantrag_kommentar_aenderungsantrag1`
    FOREIGN KEY (`aenderungsantrag_id` )
    REFERENCES `antragsgruen`.`aenderungsantrag` (`id` )
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`antrag_abo`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`antrag_abo` (
  `antrag_id` INT NOT NULL ,
  `abonnent_id` INT NOT NULL ,
  PRIMARY KEY (`antrag_id`, `abonnent_id`) ,
  INDEX `fk_antrag_has_person1_abonnent_idx` (`abonnent_id` ASC) ,
  INDEX `fk_antrag_has_person1_antrag_idx` (`antrag_id` ASC) ,
  CONSTRAINT `fk_antrag_has_person1_antrag`
    FOREIGN KEY (`antrag_id` )
    REFERENCES `antragsgruen`.`antrag` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_antrag_has_person1_abonnent`
    FOREIGN KEY (`abonnent_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`texte`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`texte` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `text_id` VARCHAR(20) NOT NULL ,
  `veranstaltung_id` SMALLINT NULL ,
  `text` MEDIUMTEXT NULL ,
  `edit_datum` TIMESTAMP NULL DEFAULT NOW() ,
  `edit_person` INT NOT NULL ,
  INDEX `fk_texte_veranstaltung1_idx` (`veranstaltung_id` ASC) ,
  INDEX `fk_texte_person1_idx` (`edit_person` ASC) ,
  UNIQUE INDEX `veranstaltung_id_UNIQUE` (`text_id` ASC, `veranstaltung_id` ASC) ,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `fk_texte_veranstaltung1`
    FOREIGN KEY (`veranstaltung_id` )
    REFERENCES `antragsgruen`.`veranstaltung` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_texte_person1`
    FOREIGN KEY (`edit_person` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `antragsgruen`.`cache`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`cache` (
  `id` CHAR(32) NOT NULL ,
  `datum` TIMESTAMP NULL ,
  `daten` LONGBLOB NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`veranstaltung_person`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`veranstaltung_person` (
  `veranstaltung_id` SMALLINT NOT NULL ,
  `person_id` INT NOT NULL ,
  `rolle` ENUM('admin', 'dabei', 'delegiert', 'abo') NULL ,
  PRIMARY KEY (`veranstaltung_id`, `person_id`) ,
  INDEX `fk_veranstaltung_has_person_person2_idx` (`person_id` ASC) ,
  INDEX `fk_veranstaltung_has_person_veranstaltung2_idx` (`veranstaltung_id` ASC) ,
  CONSTRAINT `fk_veranstaltung_has_person_veranstaltung2`
    FOREIGN KEY (`veranstaltung_id` )
    REFERENCES `antragsgruen`.`veranstaltung` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_veranstaltung_has_person_person2`
    FOREIGN KEY (`person_id` )
    REFERENCES `antragsgruen`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `antragsgruen`.`antrag_kommentar_unterstuetzer`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `antragsgruen`.`antrag_kommentar_unterstuetzer` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `ip_hash` CHAR(32) NULL ,
  `cookie_id` INT NULL ,
  `antrag_kommentar_id` INT NOT NULL ,
  `dafuer` TINYINT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_antrag_kommentar_unterstuetzer_antrag_kommentar1_idx` (`antrag_kommentar_id` ASC) ,
  UNIQUE INDEX `ip_hash_antrag` (`ip_hash` ASC, `antrag_kommentar_id` ASC) ,
  UNIQUE INDEX `cookie_antrag` (`cookie_id` ASC, `antrag_kommentar_id` ASC) ,
  CONSTRAINT `fk_antrag_kommentar_unterstuetzer_antrag_kommentar1`
    FOREIGN KEY (`antrag_kommentar_id` )
    REFERENCES `antragsgruen`.`antrag_kommentar` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `antragsgruen` ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
