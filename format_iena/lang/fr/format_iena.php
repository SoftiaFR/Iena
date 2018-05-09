<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
	
	/**
	 * format_iena
	 *
	 * @package    format_iena
	 * @category   format
	 * @copyright  2018 Softia/Université lorraine
	 * @author     vrignaud camille
	 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
	 */
	
	defined('MOODLE_INTERNAL') || die();
	
	$string['pluginname'] = 'iena format';
	$string['currentsection'] = 'La section';
	$string['editsection'] = 'Modifier section';
	$string['deletesection'] = 'Supprimer section';
	$string['sectionname'] = 'Section';
	$string['section0name'] = 'General';
	$string['hidefromothers'] = 'Cacher section';
	$string['showfromothers'] = 'Voir section';
	$string['showdefaultsectionname'] = 'Show the default sections name';
	$string['showdefaultsectionname_help'] = 'If no name is set for the section will not show anything.<br>
By definition an unnamed section is displayed as <strong>section [N]</strong>.';
	$string['yes'] = 'Oui';
	$string['no'] = 'Non';
	$string['sectionposition'] = 'Section zero position';
	
	$string['name'] = 'Nom';
	$string['summary'] = 'Résumé';
	$string['modalite'] = 'Modalité';
	$string['notif'] = 'Notification';
	$string['notif_summary'] = 'Une notification peut être envoyée à l étudiant par mail. Elle lui rapplera de consulter la section et contiendra diverses informations (nom du cours, lien vers le cours, nom de la section, date le la section et modalité';
	$string['in_presence'] = 'En présence';
	$string['not_presence'] = 'A distance';
	$string['days_before'] = 'Un certain nombre de jours avant la séance';
	$string['days_after'] = 'Un certain nombre de jours après la séance';
	$string['days_same'] = 'Le jour même';
	$string['nb_days_before'] = 'jours avant';
	$string['nb_days_after'] = 'jours après';
	$string['hide_section'] = 'Cacher la section';
	$string['hide_section_summary'] = 'Restreindre l\'accès à la séance et la cacher avant une certaine date';
	$string['hide_option_1'] = 'Ne pas restreindre l\'accès avant une date';
	$string['hide_option_2'] = 'Avant la date de la séance';
	$string['hide_option_3'] = 'Avant la date de la notification';
	$string['hide_bread_crum'] = 'Afficher la barre de progression';
	$string['hide_icon_message'] = 'Afficher l\'icone messagerie';
	$string['modules_for'] = 'Activités suivies pour';
	$string['all_course'] = 'Tout le Cours';
	$string['students'] = 'Etudiants';
	$string['not_done'] = 'n\'ayant pas tout achevé';
	$string['all_students'] = 'tous les étudiants';
	$string['student'] = 'Eleve';
	$string['send_message'] = 'Envoyer un message';
	$string['for_section_select'] = 'Pour la section sélectionnée, vous aurez un indicateur du nombre d\'étudiant qui n\'ont pas achevé les activités ci-dessous.';
	$string['cancel'] = 'Annuler';
	$string['save'] = 'Enregistrer';
	$string['check_completion'] = 'Surveiller l\'achèvement';
	$string['indic_suivi'] = 'Indicateurs de suivi de la section';
	$string['form_not_defined'] = 'Non renseigné';
	$string['settings_section_form'] = 'Paramètre de la section';
	$string['course'] = 'Cours';
	$string['link'] = 'Lien';
	$string['prof'] = 'Professeur';
	$string['section'] = 'Section';
	$string['hide_section'] = 'CACHER la section ';
	$string['show_section'] = 'MONTRER la section ';
	$string['sdn_msg_to'] = 'Envoyer un message à ';
