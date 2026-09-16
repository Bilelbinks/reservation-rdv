<?php

/**
 * Calcule les créneaux horaires disponibles pour une date et une durée de
 * service données : génère tous les créneaux possibles entre l'ouverture et
 * la fermeture (en sautant la pause déjeuner), puis retire ceux qui
 * chevauchent un rendez-vous déjà pris ce jour-là.
 *
 * @return string[] Liste d'heures de début disponibles, format "HH:MM".
 */
function getCreneauxDisponibles(PDO $pdo, string $date, int $dureeMinutes): array
{
    $ouverture = strtotime("$date " . HEURE_OUVERTURE);
    $fermeture = strtotime("$date " . HEURE_FERMETURE);
    $pauseDebut = strtotime("$date " . PAUSE_DEBUT);
    $pauseFin = strtotime("$date " . PAUSE_FIN);
    $pasSecondes = GRANULARITE_MINUTES * 60;
    $dureeSecondes = $dureeMinutes * 60;

    // Rendez-vous déjà pris ce jour-là (peu importe le service : un seul
    // rendez-vous à la fois, donc tout chevauchement bloque le créneau).
    $stmt = $pdo->prepare(
        "SELECT heure_debut, heure_fin FROM rendez_vous WHERE date_rdv = :date"
    );
    $stmt->execute(['date' => $date]);
    $reservations = $stmt->fetchAll();

    $creneaux = [];
    for ($debut = $ouverture; $debut + $dureeSecondes <= $fermeture; $debut += $pasSecondes) {
        $fin = $debut + $dureeSecondes;

        // Le créneau ne doit pas chevaucher la pause déjeuner.
        if ($debut < $pauseFin && $pauseDebut < $fin) {
            continue;
        }

        // Le créneau ne doit chevaucher aucun rendez-vous déjà pris.
        $libre = true;
        foreach ($reservations as $rdv) {
            $rdvDebut = strtotime("$date " . $rdv['heure_debut']);
            $rdvFin = strtotime("$date " . $rdv['heure_fin']);
            if ($debut < $rdvFin && $rdvDebut < $fin) {
                $libre = false;
                break;
            }
        }

        if ($libre) {
            $creneaux[] = date('H:i', $debut);
        }
    }

    return $creneaux;
}

/**
 * Vérifie qu'une date est ouvrable : pas dans le passé, pas un jour fermé.
 */
function estDateValide(string $date): bool
{
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return false;
    }
    if ($date < date('Y-m-d')) {
        return false;
    }
    $jourSemaine = (int) date('w', $timestamp);
    return !in_array($jourSemaine, JOURS_FERMES, true);
}
