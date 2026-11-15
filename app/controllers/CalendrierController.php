<?php
/* Vue calendrier : mois (grille) ou semaine (à la manière d'un emploi du temps) */
class CalendrierController extends Controller {
    public function index() {
        $classes = Cal::classes();
        $classe = (int)($_GET['classe'] ?? 0);
        if ($classe && !isset($classes[$classe])) $classe = 0;              // classe hors périmètre : ignorée
        $restreint = Cal::restriction() !== null;                          // parent / élève
        if ($restreint && !$classe && $classes) $classe = (int)array_key_first($classes);

        $vue = ($_GET['vue'] ?? 'mois') === 'semaine' ? 'semaine' : 'mois';
        $ref = $_GET['date'] ?? date('Y-m-d');
        $d = DateTime::createFromFormat('Y-m-d', $ref) ?: new DateTime();
        $ref = $d->format('Y-m-d');

        if ($vue === 'mois') {
            $first = new DateTime($d->format('Y-m-01'));
            $start = (clone $first)->modify('-'.((int)$first->format('N') - 1).' days');     // lundi de la 1re semaine
            $end   = (clone $first)->modify('last day of this month');
            $end   = (clone $end)->modify('+'.(7 - (int)$end->format('N')).' days');          // dimanche de la dernière
            $prev  = (clone $first)->modify('-1 month')->format('Y-m-d');
            $next  = (clone $first)->modify('+1 month')->format('Y-m-d');
            $titre = Cal::MOIS[(int)$first->format('n')].' '.$first->format('Y');
        } else {
            $start = (clone $d)->modify('-'.((int)$d->format('N') - 1).' days');
            $end   = (clone $start)->modify('+6 days');
            $prev  = (clone $start)->modify('-7 days')->format('Y-m-d');
            $next  = (clone $start)->modify('+7 days')->format('Y-m-d');
            $titre = 'Semaine du '.$start->format('d/m').' au '.$end->format('d/m/Y');
        }

        $events = Cal::range($start->format('Y-m-d'), $end->format('Y-m-d'), $classe);
        $lessons = $vue === 'semaine' ? Cal::lessons($classe) : [];
        $this->view('calendrier/index', [
            'vue' => $vue, 'classes' => $classes, 'classe' => $classe, 'restreint' => $restreint,
            'start' => $start, 'end' => $end, 'ref' => $ref, 'prev' => $prev, 'next' => $next, 'titre' => $titre,
            'events' => $events, 'lessons' => $lessons,
            'canEdit' => in_array($this->user()['role'], ['admin', 'enseignant']),
            'moisRef' => (int)$d->format('n'),
        ]);
    }
}
