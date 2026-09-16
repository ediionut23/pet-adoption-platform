-- setari initiale necesare pentru export
SET SERVEROUTPUT ON SIZE UNLIMITED
SET LONG 100000
SET LONGCHUNKSIZE 100000

DECLARE
    fisier_export UTL_FILE.FILE_TYPE;
    comanda_sql VARCHAR2(32767);
    numarator NUMBER;
    text_coloana VARCHAR2(4000);
    text_constrangere VARCHAR2(4000);
    
    -- cursor pt tabele din baza de date
    CURSOR tabele_user IS
        SELECT table_name FROM user_tables
        ORDER BY table_name;
    
    -- iau coloanele pt fiecare tabela    
    CURSOR coloane_tabela(numele_tabelei VARCHAR2) IS
        SELECT column_name nume_coloana, 
               data_type tip_date, 
               data_length lungime, 
               data_precision precizie, 
               data_scale scale_nr,
               nullable poate_fi_null, 
               data_default valoare_default, 
               column_id id_coloana
        FROM user_tab_columns
        WHERE table_name = numele_tabelei
        ORDER BY column_id;
    
    -- constrangeri pt fiecare tabela    
    CURSOR constrangeri_tabela(numele_tabelei VARCHAR2) IS
        SELECT constraint_name nume_constrangere, 
               constraint_type tip_constrangere, 
               search_condition conditie_cautare,
               r_constraint_name constrangere_referinta, 
               delete_rule regula_stergere
        FROM user_constraints
        WHERE table_name = numele_tabelei
        ORDER BY 
            CASE constraint_type 
                WHEN 'P' THEN 1  -- primary key primul
                WHEN 'U' THEN 2  -- unique
                WHEN 'R' THEN 3  -- foreign key
                ELSE 4 
            END;
            
    -- coloanele care fac parte din constrangeri    
    CURSOR coloane_din_constrangere(numele_constrangerii VARCHAR2) IS
        SELECT column_name, position
        FROM user_cons_columns
        WHERE constraint_name = numele_constrangerii
        ORDER BY position;
    
    -- toti indecsii care nu-s creati automat de constrangeri    
    CURSOR indecsi_definiti_manual IS
        SELECT index_name numele_indexului, 
               table_name tabela, 
               uniqueness este_unic,
               index_type tipul_indexului
        FROM user_indexes i
        WHERE index_name NOT IN (
            SELECT constraint_name 
            FROM user_constraints 
            WHERE constraint_name IS NOT NULL
        )
        ORDER BY table_name, index_name;
    
    -- toate secventele    
    CURSOR toate_secventele IS
        SELECT sequence_name numele_secventei, 
               min_value val_min, 
               max_value val_max, 
               increment_by increment,
               cycle_flag este_ciclic,
               cache_size marime_cache, 
               last_number ultimul_nr
        FROM user_sequences
        ORDER BY sequence_name;
    
    -- obiecte cu cod (proceduri, functii, etc)    
    CURSOR obiecte_programabile IS
        SELECT object_name numele_obiectului, 
               object_type tipul_obiectului
        FROM user_objects
        WHERE object_type IN (
            'PROCEDURE', 'FUNCTION', 'PACKAGE', 
            'PACKAGE BODY', 'TYPE', 'TYPE BODY'
        )
        AND status = 'VALID'
        ORDER BY 
            CASE object_type 
                WHEN 'TYPE' THEN 1
                WHEN 'PACKAGE' THEN 2
                WHEN 'TYPE BODY' THEN 3
                WHEN 'PACKAGE BODY' THEN 4
                WHEN 'PROCEDURE' THEN 5
                WHEN 'FUNCTION' THEN 6
                ELSE 7
            END,
            object_name;
    
    -- triggeri    
    CURSOR triggeri_definiti IS
        SELECT trigger_name numele_triggerului, 
               trigger_type tipul_triggerului,
               triggering_event eveniment_declansator,
               table_name tabela_trigger,
               when_clause conditie_when,
               status stare_trigger
        FROM user_triggers
        ORDER BY table_name, trigger_name;
    
    -- viewuri    
    CURSOR viewuri_schema IS
        SELECT view_name numele_viewului, 
               text codul_view
        FROM user_views
        ORDER BY view_name;

    -- functie ajutatoare pentru definirea coloanelor
    FUNCTION construieste_definitie_coloana(
        numele_coloanei VARCHAR2,
        tipul_de_date VARCHAR2,
        lungimea_datelor NUMBER,
        precizia NUMBER,
        scala NUMBER,
        este_nullable VARCHAR2,
        valoarea_default VARCHAR2
    ) RETURN VARCHAR2 
    IS
        definitie_completa VARCHAR2(1000);
    BEGIN
        definitie_completa := numele_coloanei || ' ';
        
        IF tipul_de_date = 'VARCHAR2' THEN
            definitie_completa := definitie_completa || 'VARCHAR2(' || lungimea_datelor || ')';
        ELSIF tipul_de_date = 'CHAR' THEN
            definitie_completa := definitie_completa || 'CHAR(' || lungimea_datelor || ')';
        ELSIF tipul_de_date = 'NUMBER' THEN
            IF precizia IS NOT NULL THEN
                IF scala > 0 THEN
                    definitie_completa := definitie_completa || 'NUMBER(' || precizia || ',' || scala || ')';
                ELSE
                    definitie_completa := definitie_completa || 'NUMBER(' || precizia || ')';
                END IF;
            ELSE
                definitie_completa := definitie_completa || 'NUMBER';
            END IF;
        ELSE
            definitie_completa := definitie_completa || tipul_de_date;
        END IF;
        
        -- adaug default daca exista
        IF valoarea_default IS NOT NULL THEN
            definitie_completa := definitie_completa || ' DEFAULT ' || TRIM(valoarea_default);
        END IF;
        
        -- pun NOT NULL daca trebuie
        IF este_nullable = 'N' THEN
            definitie_completa := definitie_completa || ' NOT NULL';
        END IF;
        
        RETURN definitie_completa;
    END;

BEGIN
    -- deschidem fisierul pentru export
    fisier_export := UTL_FILE.FOPEN('MYDIR', 'schema_export_complete.sql', 'W', 32767);
    
    -- scriu header-ul in fisier
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- EXPORT SCHEMA - ' || USER);
    UTL_FILE.PUT_LINE(fisier_export, '-- Data: ' || TO_CHAR(SYSDATE, 'DD-MON-YYYY HH24:MI:SS'));
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    UTL_FILE.PUT_LINE(fisier_export, 'SET DEFINE OFF;');
    UTL_FILE.PUT_LINE(fisier_export, 'SET SQLBLANKLINES ON;');
    UTL_FILE.PUT_LINE(fisier_export, 'ALTER SESSION SET NLS_DATE_FORMAT=''DD-MON-YYYY HH24:MI:SS'';');
    UTL_FILE.PUT_LINE(fisier_export, '');

    -- mai intai exportam tabelele
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- TABELE');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR tabela IN tabele_user LOOP
        BEGIN
            UTL_FILE.PUT_LINE(fisier_export, '-- Tabela: ' || tabela.table_name);
            UTL_FILE.PUT_LINE(fisier_export, 'CREATE TABLE ' || tabela.table_name || ' (');
            
            numarator := 0;
            FOR coloana IN coloane_tabela(tabela.table_name) LOOP
                numarator := numarator + 1;
                IF numarator > 1 THEN
                    UTL_FILE.PUT_LINE(fisier_export, ',');
                END IF;
                
                text_coloana := '  ' || construieste_definitie_coloana(
                    coloana.nume_coloana, coloana.tip_date, coloana.lungime,
                    coloana.precizie, coloana.scale_nr, coloana.poate_fi_null, 
                    coloana.valoare_default
                );
                
                UTL_FILE.PUT(fisier_export, text_coloana);
            END LOOP;
            
            UTL_FILE.PUT_LINE(fisier_export, '');
            UTL_FILE.PUT_LINE(fisier_export, ');');
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
            
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la exportul tabelei ' || tabela.table_name || ': ' || SQLERRM);
                UTL_FILE.PUT_LINE(fisier_export, '');
        END;
    END LOOP;

    -- acum punem constrangerile
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- CONSTRANGERI');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    -- mai intai primary si unique
    FOR tabela IN tabele_user LOOP
        FOR constrangere IN constrangeri_tabela(tabela.table_name) LOOP
            BEGIN
                IF constrangere.tip_constrangere IN ('P', 'U') THEN
                    comanda_sql := 'ALTER TABLE ' || tabela.table_name || 
                                 ' ADD CONSTRAINT ' || constrangere.nume_constrangere;
                    
                    IF constrangere.tip_constrangere = 'P' THEN
                        comanda_sql := comanda_sql || ' PRIMARY KEY (';
                    ELSE
                        comanda_sql := comanda_sql || ' UNIQUE (';
                    END IF;
                    
                    -- adaugam coloanele constrangerii
                    numarator := 0;
                    FOR coloana IN coloane_din_constrangere(constrangere.nume_constrangere) LOOP
                        IF numarator > 0 THEN
                            comanda_sql := comanda_sql || ', ';
                        END IF;
                        comanda_sql := comanda_sql || coloana.column_name;
                        numarator := numarator + 1;
                    END LOOP;
                    
                    comanda_sql := comanda_sql || ')';
                    UTL_FILE.PUT_LINE(fisier_export, comanda_sql || ';');
                    UTL_FILE.PUT_LINE(fisier_export, '/');
                    UTL_FILE.PUT_LINE(fisier_export, '');
                END IF;
            EXCEPTION
                WHEN OTHERS THEN
                    UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la constrangerea ' || 
                                    constrangere.nume_constrangere || ': ' || SQLERRM);
            END;
        END LOOP;
    END LOOP;

    -- apoi foreign keys
    FOR tabela IN tabele_user LOOP
        FOR constrangere IN constrangeri_tabela(tabela.table_name) LOOP
            IF constrangere.tip_constrangere = 'R' THEN
                BEGIN
                    comanda_sql := 'ALTER TABLE ' || tabela.table_name || 
                                 ' ADD CONSTRAINT ' || constrangere.nume_constrangere ||
                                 ' FOREIGN KEY (';
                    
                    -- coloanele din FK
                    numarator := 0;
                    FOR coloana IN coloane_din_constrangere(constrangere.nume_constrangere) LOOP
                        IF numarator > 0 THEN
                            comanda_sql := comanda_sql || ', ';
                        END IF;
                        comanda_sql := comanda_sql || coloana.column_name;
                        numarator := numarator + 1;
                    END LOOP;
                    
                    comanda_sql := comanda_sql || ') REFERENCES ';
                    
                    -- gasim tabela referita
                    SELECT table_name INTO text_coloana
                    FROM user_constraints
                    WHERE constraint_name = constrangere.constrangere_referinta;
                    
                    comanda_sql := comanda_sql || text_coloana || ' (';
                    
                    -- coloanele referite
                    numarator := 0;
                    FOR coloana IN coloane_din_constrangere(constrangere.constrangere_referinta) LOOP
                        IF numarator > 0 THEN
                            comanda_sql := comanda_sql || ', ';
                        END IF;
                        comanda_sql := comanda_sql || coloana.column_name;
                        numarator := numarator + 1;
                    END LOOP;
                    
                    comanda_sql := comanda_sql || ')';
                    
                    -- regula de stergere daca exista
                    IF constrangere.regula_stergere != 'NO ACTION' THEN
                        comanda_sql := comanda_sql || ' ON DELETE ' || constrangere.regula_stergere;
                    END IF;
                    
                    UTL_FILE.PUT_LINE(fisier_export, comanda_sql || ';');
                    UTL_FILE.PUT_LINE(fisier_export, '/');
                    UTL_FILE.PUT_LINE(fisier_export, '');
                EXCEPTION
                    WHEN OTHERS THEN
                        UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la FK ' || 
                                        constrangere.nume_constrangere || ': ' || SQLERRM);
                END;
            END IF;
        END LOOP;
    END LOOP;

    -- exportam indecsii
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- INDECSI');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR idx IN indecsi_definiti_manual LOOP
        BEGIN
            comanda_sql := 'CREATE ';
            IF idx.este_unic = 'UNIQUE' THEN
                comanda_sql := comanda_sql || 'UNIQUE ';
            END IF;
            
            comanda_sql := comanda_sql || 'INDEX ' || idx.numele_indexului || 
                         ' ON ' || idx.tabela || ' (';
            
            -- coloanele indexului
            numarator := 0;
            FOR col_idx IN (SELECT column_name, column_position
                          FROM user_ind_columns
                          WHERE index_name = idx.numele_indexului
                          ORDER BY column_position) LOOP
                IF numarator > 0 THEN
                    comanda_sql := comanda_sql || ', ';
                END IF;
                comanda_sql := comanda_sql || col_idx.column_name;
                numarator := numarator + 1;
            END LOOP;
            
            comanda_sql := comanda_sql || ')';
            
            UTL_FILE.PUT_LINE(fisier_export, comanda_sql || ';');
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la indexul ' || 
                                idx.numele_indexului || ': ' || SQLERRM);
        END;
    END LOOP;

    -- exportam secventele
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- SECVENTE');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR secv IN toate_secventele LOOP
        BEGIN
            comanda_sql := 'CREATE SEQUENCE ' || secv.numele_secventei;
            
            IF secv.val_min IS NOT NULL THEN
                comanda_sql := comanda_sql || ' MINVALUE ' || secv.val_min;
            END IF;
            
            IF secv.val_max IS NOT NULL THEN
                comanda_sql := comanda_sql || ' MAXVALUE ' || secv.val_max;
            END IF;
            
            IF secv.increment != 1 THEN
                comanda_sql := comanda_sql || ' INCREMENT BY ' || secv.increment;
            END IF;
            
            IF secv.este_ciclic = 'Y' THEN
                comanda_sql := comanda_sql || ' CYCLE';
            END IF;
            
            IF secv.marime_cache > 0 THEN
                comanda_sql := comanda_sql || ' CACHE ' || secv.marime_cache;
            ELSE
                comanda_sql := comanda_sql || ' NOCACHE';
            END IF;
            
            UTL_FILE.PUT_LINE(fisier_export, comanda_sql || ';');
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la secventa ' || 
                                secv.numele_secventei || ': ' || SQLERRM);
        END;
    END LOOP;

    -- exportam viewurile
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- VIEWURI');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR v IN viewuri_schema LOOP
        BEGIN
            UTL_FILE.PUT_LINE(fisier_export, 'CREATE OR REPLACE VIEW ' || 
                            v.numele_viewului || ' AS');
            UTL_FILE.PUT_LINE(fisier_export, v.codul_view);
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la view ' || 
                                v.numele_viewului || ': ' || SQLERRM);
        END;
    END LOOP;

    -- exportam obiectele programabile
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- PROCEDURI, FUNCTII, PACHETE');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR obj IN obiecte_programabile LOOP
        BEGIN
            UTL_FILE.PUT_LINE(fisier_export, 'CREATE OR REPLACE ');
            
            FOR src IN (SELECT text 
                       FROM user_source 
                       WHERE name = obj.numele_obiectului 
                         AND type = obj.tipul_obiectului 
                       ORDER BY line) LOOP
                UTL_FILE.PUT(fisier_export, src.text);
            END LOOP;
            
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la ' || obj.tipul_obiectului || 
                                ' ' || obj.numele_obiectului || ': ' || SQLERRM);
        END;
    END LOOP;

    -- exportam triggerii
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- TRIGGERI');
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '');
    
    FOR trg IN triggeri_definiti LOOP
        BEGIN
            UTL_FILE.PUT_LINE(fisier_export, 'CREATE OR REPLACE ');
            
            FOR src IN (SELECT text 
                       FROM user_source 
                       WHERE name = trg.numele_triggerului 
                         AND type = 'TRIGGER' 
                       ORDER BY line) LOOP
                UTL_FILE.PUT(fisier_export, src.text);
            END LOOP;
            
            UTL_FILE.PUT_LINE(fisier_export, '/');
            UTL_FILE.PUT_LINE(fisier_export, '');
        EXCEPTION
            WHEN OTHERS THEN
                UTL_FILE.PUT_LINE(fisier_export, '-- Eroare la trigger ' || 
                                trg.numele_triggerului || ': ' || SQLERRM);
        END;
    END LOOP;

    -- footer
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    UTL_FILE.PUT_LINE(fisier_export, '-- EXPORT FINALIZAT');
    UTL_FILE.PUT_LINE(fisier_export, '-- ' || TO_CHAR(SYSDATE, 'DD-MON-YYYY HH24:MI:SS'));
    UTL_FILE.PUT_LINE(fisier_export, '-- ================================');
    
    -- inchidem fisierul
    UTL_FILE.FCLOSE(fisier_export);
    
    DBMS_OUTPUT.PUT_LINE('Export finalizat cu succes!');
    DBMS_OUTPUT.PUT_LINE('Fisier generat: schema_export_complete.sql');
    DBMS_OUTPUT.PUT_LINE('Locatie: directorul MYDIR');
    
EXCEPTION
    WHEN OTHERS THEN
        IF UTL_FILE.IS_OPEN(fisier_export) THEN
            UTL_FILE.FCLOSE(fisier_export);
        END IF;
        RAISE_APPLICATION_ERROR(-20001, 'Eroare la export: ' || SQLERRM);
END;
/