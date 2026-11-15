<?php
/* Contrôleur CRUD générique : ajout, modification, suppression logique + journalisation */
abstract class CrudController extends Controller {
    protected $table, $slug, $title;
    protected $fields = [];          // 'col' => ['label','type','required','options','ref','min','max']
    protected $list = [];            // colonnes affichées dans le tableau
    protected $writeRoles = ['admin'];
    protected $hasYear = true;
    protected $pk = 'id';            // clé primaire (id_ecole pour la table schools)
    protected $scoped = true;        // table filtrée par id_ecole (école de la session)
    protected $rowLinks = [];        // [['Reçu','paiement/recu/{id}']]

    protected function canWrite() { return in_array($_SESSION['user']['role'], $this->writeRoles); }
    protected function needWrite() { if (!$this->canWrite()) $this->deny(); }

    // Hooks à surcharger
    protected function validateExtra($d, $id) { return null; }
    protected function beforeDelete($id) { return null; }
    protected function afterStore($id, $d) {}

    // Filtre école : toujours basé sur la session (le superadmin voit tout, uniquement sur la table users)
    protected function scope($alias = '') {
        if (!$this->scoped || is_super()) return '';
        return ' AND '.($alias ? $alias.'.' : '').'id_ecole = '.school_id();
    }
    protected function pkAlias() { return $this->pk === 'id' ? '' : ", {$this->pk} AS id"; }
    protected function refOptions($f) {
        $r = $f['ref'];
        $sc = (isset($r['unscoped']) || is_super()) ? '' : ' AND id_ecole = '.school_id();
        $sql = "SELECT ".($r['pk'] ?? 'id').", {$r['label']} AS lbl FROM {$r['table']} WHERE deleted_at IS NULL"
             . $sc . (isset($r['where']) ? " AND {$r['where']}" : '') . ' ORDER BY lbl';
        return DB::q($sql)->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    protected function maps() {
        $m = [];
        foreach ($this->fields as $k => $f) {
            if (isset($f['ref'])) $m[$k] = $this->refOptions($f);
            elseif (isset($f['options'])) $m[$k] = $f['options'];
        }
        return $m;
    }
    protected function collect() {
        $d = [];
        foreach ($this->fields as $k => $f) {
            $v = trim($_POST[$k] ?? '');
            $d[$k] = ($v === '') ? null : $v;
        }
        return $d;
    }
    protected function validate($d, $id) {
        $maps = $this->maps();
        foreach ($this->fields as $k => $f) {
            if ($d[$k] !== null && isset($maps[$k]) && !array_key_exists($d[$k], $maps[$k])) return 'Valeur invalide pour « '.$f['label'].' ».';
            if (!empty($f['required']) && $d[$k] === null) return 'Le champ « '.$f['label'].' » est obligatoire.';
            if ($d[$k] !== null && ($f['type'] ?? '') === 'number') {
                if (!is_numeric($d[$k])) return 'Le champ « '.$f['label'].' » doit être numérique.';
                if (isset($f['min']) && $d[$k] < $f['min']) return '« '.$f['label'].' » doit être ≥ '.$f['min'].'.';
                if (isset($f['max']) && $d[$k] > $f['max']) return '« '.$f['label'].' » doit être ≤ '.$f['max'].'.';
            }
        }
        return $this->validateExtra($d, $id);
    }
    protected function selectRows() {
        $sql = "SELECT *".$this->pkAlias()." FROM {$this->table} WHERE deleted_at IS NULL".$this->scope();
        $p = [];
        if ($this->hasYear) { $sql .= ' AND annee_scolaire = ?'; $p[] = ANNEE; }
        return DB::all($sql.' ORDER BY '.$this->pk.' DESC', $p);
    }

    public function index() {
        $this->view('crud/index', [
            'title' => $this->title, 'slug' => $this->slug, 'fields' => $this->fields, 'list' => $this->list,
            'rows' => $this->selectRows(), 'maps' => $this->maps(), 'canWrite' => $this->canWrite(), 'rowLinks' => $this->rowLinks,
        ]);
    }
    public function create() {
        $this->needWrite();
        $this->form(null, [], null);
    }
    protected function form($id, $row, $error) {
        $this->view('crud/form', [
            'title' => $this->title, 'slug' => $this->slug, 'fields' => $this->fields,
            'maps' => $this->maps(), 'row' => $row, 'id' => $id, 'error' => $error,
        ]);
    }
    public function store() {
        $this->needWrite(); $this->post();
        $d = $this->collect();
        if ($err = $this->validate($d, null)) return $this->form(null, $d, $err);
        if ($this->hasYear) $d['annee_scolaire'] = ANNEE;
        if ($this->scoped && !is_super()) $d['id_ecole'] = school_id();   // l'école vient de la session
        $cols = array_keys($d);
        DB::q("INSERT INTO {$this->table} (".implode(',', $cols).') VALUES ('.implode(',', array_fill(0, count($cols), '?')).')', array_values($d));
        $id = DB::id();
        journal($this->table, $id, 'création', null, $d);
        $this->afterStore($id, $d);
        $this->flash('ok', 'Enregistrement ajouté avec succès.');
        $this->redirect($this->slug);
    }
    protected function find($id) {
        $row = DB::one("SELECT *".$this->pkAlias()." FROM {$this->table} WHERE {$this->pk} = ? AND deleted_at IS NULL".$this->scope(), [(int)$id]);
        if (!$row) { http_response_code(404); die('Enregistrement introuvable.'); }
        return $row;
    }
    public function edit($id = 0) {
        $this->needWrite();
        $this->form((int)$id, $this->find($id), null);
    }
    public function update($id = 0) {
        $this->needWrite(); $this->post();
        $old = $this->find($id);
        $d = $this->collect();
        if ($err = $this->validate($d, (int)$id)) return $this->form((int)$id, $d, $err);
        $set = implode(',', array_map(function ($c) { return "$c = ?"; }, array_keys($d)));
        DB::q("UPDATE {$this->table} SET $set WHERE {$this->pk} = ?".$this->scope(), array_merge(array_values($d), [(int)$id]));
        $diffOld = []; $diffNew = [];
        foreach ($d as $k => $v) if ((string)$old[$k] !== (string)$v) { $diffOld[$k] = $old[$k]; $diffNew[$k] = $v; }
        journal($this->table, (int)$id, 'modification', $diffOld, $diffNew);
        $this->flash('ok', 'Enregistrement modifié.');
        $this->redirect($this->slug);
    }
    public function delete($id = 0) {
        $this->needWrite(); $this->post();
        $this->find($id);
        if ($err = $this->beforeDelete((int)$id)) { $this->flash('err', $err); $this->redirect($this->slug); }
        DB::q("UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->pk} = ?".$this->scope(), [(int)$id]);
        journal($this->table, (int)$id, 'suppression logique');
        $this->flash('ok', 'Enregistrement supprimé.');
        $this->redirect($this->slug);
    }
}
