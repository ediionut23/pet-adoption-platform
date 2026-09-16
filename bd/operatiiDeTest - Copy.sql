-- Script PL/SQL pentru afișarea formatată a datelor din toate tabelele
SET SERVEROUTPUT ON SIZE UNLIMITED;
SET LINESIZE 300;
SET PAGESIZE 999;

DECLARE
    -- Variabile pentru a stoca informații despre coloane și date
    v_column_name VARCHAR2(100);
    v_column_value VARCHAR2(4000);
    v_column_count NUMBER;
    v_separator VARCHAR2(1000);
    v_header VARCHAR2(1000);
    v_line VARCHAR2(1000);
    
    -- Cursoare pentru tabelele principale
    
    -- 1. Cursor pentru USERS
    CURSOR c_users IS
        SELECT id, name, surname, email, location, 
               CASE WHEN is_family = 1 THEN 'Da' ELSE 'Nu' END AS is_family,
               latitude, longitude, rol
        FROM users
        ORDER BY id;
        
    -- 2. Cursor pentru PETS (actualizat cu noile câmpuri)
    CURSOR c_pets IS
        SELECT id, name, species, breed, age, gender, health_status, 
               SUBSTR(description, 1, 50) || CASE WHEN LENGTH(description) > 50 THEN '...' ELSE '' END AS description_short,
               CASE WHEN available_for_adoption = 1 THEN 'Da' ELSE 'Nu' END AS available,
               adoption_address, owner_id,
               personality_description,
               activity_description,
               diet_description,
               household_activity,
               household_environment,
               other_pets,
               color,
               marime,
               CASE WHEN spayed_neutered = 1 THEN 'Da' ELSE 'Nu' END AS spayed_neutered,
               time_at_current_home,
               reason_for_rehoming,
               CASE WHEN flea_treatment = 1 THEN 'Da' ELSE 'Nu' END AS flea_treatment,
               current_owner_description
        FROM pets
        ORDER BY id;
        
    -- 3. Cursor pentru ADOPTIONS
    CURSOR c_adoptions IS
        SELECT a.id, a.pet_id, p.name AS pet_name, a.adopter_id, 
               u.name || ' ' || u.surname AS adopter_name,
               TO_CHAR(a.adoption_date, 'DD-MM-YYYY') AS adoption_date, a.status
        FROM adoptions a
        JOIN pets p ON a.pet_id = p.id
        JOIN users u ON a.adopter_id = u.id
        ORDER BY a.id;
        
    -- 4. Cursor pentru FEEDING_SCHEDULE
    CURSOR c_feeding IS
        SELECT fs.id, fs.pet_id, p.name AS pet_name, 
               TO_CHAR(fs.time, 'DD-MM-YYYY HH24:MI') AS feeding_time,
               fs.food_description, fs.frequency
        FROM feeding_schedule fs
        JOIN pets p ON fs.pet_id = p.id
        ORDER BY fs.id;
        
    -- 5. Cursor pentru RESTRICTIONS
    CURSOR c_restrictions IS
        SELECT r.id, r.pet_id, p.name AS pet_name, r.description
        FROM restrictions r
        JOIN pets p ON r.pet_id = p.id
        ORDER BY r.id;
        
    -- 6. Cursor pentru MEDIA
    CURSOR c_media IS
        SELECT m.id, m.pet_id, p.name AS pet_name, m.type, 
               m.url, TO_CHAR(m.upload_date, 'DD-MM-YYYY') AS upload_date
        FROM media m
        JOIN pets p ON m.pet_id = p.id
        ORDER BY m.id;
        
    -- 7. Cursor pentru MEDICAL_HISTORY
    CURSOR c_medical IS
        SELECT mh.id, mh.pet_id, p.name AS pet_name, 
               TO_CHAR(mh.record_date, 'DD-MM-YYYY') AS record_date,
               mh.description, mh.first_aid_method
        FROM medical_history mh
        JOIN pets p ON mh.pet_id = p.id
        ORDER BY mh.id;
        
    -- 8. Cursor pentru RSS_FEED
    CURSOR c_rss IS
        SELECT rf.id, rf.title, 
               SUBSTR(rf.content, 1, 50) || CASE WHEN LENGTH(rf.content) > 50 THEN '...' ELSE '' END AS content_short,
               TO_CHAR(rf.date_posted, 'DD-MM-YYYY') AS date_posted,
               rf.pet_id, p.name AS pet_name, rf.location, rf.popularity_score,
               CASE WHEN rf.is_general_news = 1 THEN 'Da' ELSE 'Nu' END AS is_general
        FROM rss_feed rf
        LEFT JOIN pets p ON rf.pet_id = p.id
        ORDER BY rf.id;
        
    -- Procedură pentru afișarea unei linii de separare
    PROCEDURE print_separator(p_length IN NUMBER) IS
        v_sep VARCHAR2(1000) := '';
    BEGIN
        FOR i IN 1..p_length LOOP
            v_sep := v_sep || '-';
        END LOOP;
        DBMS_OUTPUT.PUT_LINE(v_sep);
    END;
    
BEGIN
    -- Afișarea unui antet pentru întregul raport
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '======== RAPORT COMPLET CU DATELE DIN APLICAȚIA DE ADOPȚIE ANIMALE ========' || CHR(10));
    
    ------------------------------------------
    -- 1. Afișarea datelor din tabelul USERS
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '1. UTILIZATORI' || CHR(10));
    print_separator(100);
    DBMS_OUTPUT.PUT_LINE('ID  | NUME       | PRENUME    | EMAIL                  | LOCAȚIE         | FAMILIE | ROL');
    print_separator(100);
    
    FOR user_rec IN c_users LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(user_rec.id, 4) || '| ' ||
            RPAD(user_rec.name, 11) || '| ' ||
            RPAD(user_rec.surname, 11) || '| ' ||
            RPAD(user_rec.email, 23) || '| ' ||
            RPAD(user_rec.location, 17) || '| ' ||
            RPAD(user_rec.is_family, 8) || '| ' ||
            user_rec.rol
        );
    END LOOP;
    print_separator(100);
    
    ------------------------------------------
    -- 2. Afișarea datelor din tabelul PETS (actualizat)
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '2. ANIMALE' || CHR(10));
    
    FOR pet_rec IN c_pets LOOP
        print_separator(120);
        DBMS_OUTPUT.PUT_LINE('ID: ' || pet_rec.id);
        DBMS_OUTPUT.PUT_LINE('Nume: ' || pet_rec.name);
        DBMS_OUTPUT.PUT_LINE('Specie: ' || pet_rec.species);
        DBMS_OUTPUT.PUT_LINE('Rasă: ' || pet_rec.breed);
        DBMS_OUTPUT.PUT_LINE('Vârstă: ' || pet_rec.age);
        DBMS_OUTPUT.PUT_LINE('Gen: ' || pet_rec.gender);
        DBMS_OUTPUT.PUT_LINE('Stare sănătate: ' || pet_rec.health_status);
        DBMS_OUTPUT.PUT_LINE('Disponibil pentru adopție: ' || pet_rec.available);
        DBMS_OUTPUT.PUT_LINE('Adresă adopție: ' || pet_rec.adoption_address);
        DBMS_OUTPUT.PUT_LINE('ID Proprietar: ' || pet_rec.owner_id);
        DBMS_OUTPUT.PUT_LINE('Descriere: ' || pet_rec.description_short);
        
        -- Noile câmpuri
        DBMS_OUTPUT.PUT_LINE(CHR(10) || '--- Informații detaliate ---');
        DBMS_OUTPUT.PUT_LINE('Personalitate: ' || pet_rec.personality_description);
        DBMS_OUTPUT.PUT_LINE('Activitate: ' || pet_rec.activity_description);
        DBMS_OUTPUT.PUT_LINE('Dietă: ' || pet_rec.diet_description);
        DBMS_OUTPUT.PUT_LINE('Activitate gospodărie: ' || pet_rec.household_activity);
        DBMS_OUTPUT.PUT_LINE('Mediu gospodărie: ' || pet_rec.household_environment);
        DBMS_OUTPUT.PUT_LINE('Alte animale: ' || pet_rec.other_pets);
        DBMS_OUTPUT.PUT_LINE('Culoare: ' || pet_rec.color);
        DBMS_OUTPUT.PUT_LINE('Mărime: ' || pet_rec.marime);
        DBMS_OUTPUT.PUT_LINE('Sterilizat/Castrat: ' || pet_rec.spayed_neutered);
        DBMS_OUTPUT.PUT_LINE('Timp la casa curentă: ' || pet_rec.time_at_current_home);
        DBMS_OUTPUT.PUT_LINE('Motiv pentru relocare: ' || pet_rec.reason_for_rehoming);
        DBMS_OUTPUT.PUT_LINE('Tratament purici: ' || pet_rec.flea_treatment);
        DBMS_OUTPUT.PUT_LINE('Descriere proprietar curent: ' || pet_rec.current_owner_description);
        DBMS_OUTPUT.PUT_LINE('');
    END LOOP;
    print_separator(120);
    
    ------------------------------------------
    -- 3. Afișarea datelor din tabelul ADOPTIONS
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '3. CERERI DE ADOPȚIE' || CHR(10));
    print_separator(100);
    DBMS_OUTPUT.PUT_LINE('ID  | ANIMAL ID | NUME ANIMAL | ADOPTATOR ID | NUME ADOPTATOR      | DATA       | STATUS');
    print_separator(100);
    
    FOR adoption_rec IN c_adoptions LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(adoption_rec.id, 4) || '| ' ||
            RPAD(adoption_rec.pet_id, 10) || '| ' ||
            RPAD(adoption_rec.pet_name, 12) || '| ' ||
            RPAD(adoption_rec.adopter_id, 13) || '| ' ||
            RPAD(adoption_rec.adopter_name, 20) || '| ' ||
            RPAD(adoption_rec.adoption_date, 11) || '| ' ||
            adoption_rec.status
        );
    END LOOP;
    print_separator(100);
    
    ------------------------------------------
    -- 4. Afișarea datelor din tabelul FEEDING_SCHEDULE
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '4. PROGRAM DE HRĂNIRE' || CHR(10));
    print_separator(110);
    DBMS_OUTPUT.PUT_LINE('ID  | ANIMAL ID | NUME ANIMAL | MOMENT HRĂNIRE     | TIP HRANĂ                | FRECVENȚĂ');
    print_separator(110);
    
    FOR feeding_rec IN c_feeding LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(feeding_rec.id, 4) || '| ' ||
            RPAD(feeding_rec.pet_id, 10) || '| ' ||
            RPAD(feeding_rec.pet_name, 12) || '| ' ||
            RPAD(feeding_rec.feeding_time, 19) || '| ' ||
            RPAD(feeding_rec.food_description, 25) || '| ' ||
            feeding_rec.frequency
        );
    END LOOP;
    print_separator(110);
    
    ------------------------------------------
    -- 5. Afișarea datelor din tabelul RESTRICTIONS
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '5. RESTRICȚII' || CHR(10));
    print_separator(100);
    DBMS_OUTPUT.PUT_LINE('ID  | ANIMAL ID | NUME ANIMAL | DESCRIERE RESTRICȚIE');
    print_separator(100);
    
    FOR restriction_rec IN c_restrictions LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(restriction_rec.id, 4) || '| ' ||
            RPAD(restriction_rec.pet_id, 10) || '| ' ||
            RPAD(restriction_rec.pet_name, 12) || '| ' ||
            restriction_rec.description
        );
    END LOOP;
    print_separator(100);
    
    ------------------------------------------
    -- 6. Afișarea datelor din tabelul MEDIA
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '6. MEDIA' || CHR(10));
    print_separator(100);
    DBMS_OUTPUT.PUT_LINE('ID  | ANIMAL ID | NUME ANIMAL | TIP    | DATA       | URL');
    print_separator(100);
    
    FOR media_rec IN c_media LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(media_rec.id, 4) || '| ' ||
            RPAD(media_rec.pet_id, 10) || '| ' ||
            RPAD(media_rec.pet_name, 12) || '| ' ||
            RPAD(media_rec.type, 7) || '| ' ||
            RPAD(media_rec.upload_date, 11) || '| ' ||
            media_rec.url
        );
    END LOOP;
    print_separator(100);
    
    ------------------------------------------
    -- 7. Afișarea datelor din tabelul MEDICAL_HISTORY
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '7. ISTORIC MEDICAL' || CHR(10));
    print_separator(110);
    DBMS_OUTPUT.PUT_LINE('ID  | ANIMAL ID | NUME ANIMAL | DATA       | DESCRIERE                               | PRIM AJUTOR');
    print_separator(110);
    
    FOR medical_rec IN c_medical LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(medical_rec.id, 4) || '| ' ||
            RPAD(medical_rec.pet_id, 10) || '| ' ||
            RPAD(medical_rec.pet_name, 12) || '| ' ||
            RPAD(medical_rec.record_date, 11) || '| ' ||
            RPAD(medical_rec.description, 40) || '| ' ||
            medical_rec.first_aid_method
        );
    END LOOP;
    print_separator(110);
    
    ------------------------------------------
    -- 8. Afișarea datelor din tabelul RSS_FEED
    ------------------------------------------
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '8. FEED RSS' || CHR(10));
    print_separator(120);
    DBMS_OUTPUT.PUT_LINE('ID  | TITLU                    | DATA       | ANIMAL ID | NUME ANIMAL | LOCAȚIE         | SCOR  | GENERAL');
    print_separator(120);
    
    FOR rss_rec IN c_rss LOOP
        DBMS_OUTPUT.PUT_LINE(
            RPAD(rss_rec.id, 4) || '| ' ||
            RPAD(rss_rec.title, 25) || '| ' ||
            RPAD(rss_rec.date_posted, 11) || '| ' ||
            RPAD(NVL(TO_CHAR(rss_rec.pet_id), '-'), 10) || '| ' ||
            RPAD(NVL(rss_rec.pet_name, '-'), 12) || '| ' ||
            RPAD(rss_rec.location, 17) || '| ' ||
            RPAD(rss_rec.popularity_score, 7) || '| ' ||
            rss_rec.is_general
        );
        -- Afișarea conținutului pe o linie separată
        DBMS_OUTPUT.PUT_LINE('     Conținut: ' || rss_rec.content_short);
        DBMS_OUTPUT.PUT_LINE('');
    END LOOP;
    print_separator(120);
    
    -- Mesaj final
    DBMS_OUTPUT.PUT_LINE(CHR(10) || '======== SFÂRȘIT RAPORT ========' || CHR(10));
END;
/
