CREATE OR REPLACE VIEW cursuri_profesori AS
SELECT p.nume, p.prenume, c.titlu_curs
FROM profesori p
JOIN didactic d ON p.id = d.id_profesor
JOIN cursuri c ON c.id = d.id_curs

CREATE OR REPLACE TRIGGER insert_cursuri_profesori
INSTEAD OF INSERT on cursuri_profesori
FOR EACH ROW
DECLARE
v_id_profesor profesori.id%TYPE;
v_id_curs cursuri.id%TYPE;
BEGIN
    SELECT id INTO v_id_profesor
    FROM profesori
    WHERE nume=:NEW.nume AND prenume = :NEW.prenume
    FETCH FIRST ROW ONLY;
    
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        SELECT NVL(MAX(id), 0) + 1 INTO v_id_profesor FROM profesori;
        INSERT INTO profesori(id,nume, prenume)
        VALUES (v_id_profesor, :NEW.nume, :NEW.prenume);
END;
BEGIN
    SELECT id INTO v_id_curs
    FROM cursuri
    WHERE titlu = :NEW.titlu
    FETCH FIRST ROW ONLY;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        SELECT NVL(MAX(id),0) + 1 INTO v_id_curs FROM cursuri;
        




INSERT INTO cursuri_profesori(nume, prenume, titlu)
VALUES ('Popescu', 'Ion', 'Bazele Informaticei');

INSERT INTO cursuri_profesori(nume, prenume, titlu)
VALUES ('Popescu', 'Ion', 'Programare C');



