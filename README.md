# SportovyKlub
Registračný systém pre športový klub

# Migrácia
Pre správne fungovanie prihlasovacej aplikácie je potrebné na databáze spustiť nasledovné dopyty:
- `UPDATE "Pouzivatelia" SET id_oddiel=NULL WHERE id_oddiel='';`
- `ALTER TABLE Kategorie_pre ADD COLUMN api_comp_cat_id INTEGER NOT NULL DEFAULT 0;`
