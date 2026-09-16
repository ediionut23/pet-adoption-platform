CREATE OR REPLACE FUNCTION get_user_adoption_status(p_user_id IN NUMBER)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT 
            af.id as form_id,
            af.status,
            af.form_submitted_date,
            p.id as pet_id,
            p.name as pet_name,
            p.species,
            NVL(
                (
                    SELECT url 
                    FROM (
                        SELECT url
                        FROM media 
                        WHERE pet_id = p.id 
                        AND type = 'photo'
                        ORDER BY upload_date ASC
                    ) 
                    WHERE ROWNUM = 1
                ),
                NULL
            ) as pet_image
        FROM adoption_form af
        JOIN pets p ON af.pet_id = p.id
        WHERE af.user_id = p_user_id
        ORDER BY af.form_submitted_date DESC;
    
    RETURN v_result;
END get_user_adoption_status;
/