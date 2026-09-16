CREATE OR REPLACE FUNCTION get_approved_pet_image(p_user_id IN NUMBER)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT 
            CASE 
                WHEN m.url IS NOT NULL THEN m.url 
                ELSE 'stiluri/imagini/' || LOWER(p.species) || '.png'
            END as image_url,
            p.name as pet_name,
            p.species
        FROM adoption_form af
        JOIN pets p ON af.pet_id = p.id
        LEFT JOIN (
            SELECT pet_id, url
            FROM (
                SELECT pet_id, url,
                       ROW_NUMBER() OVER (PARTITION BY pet_id ORDER BY upload_date ASC) as rn
                FROM media
                WHERE type = 'photo'
            )
            WHERE rn = 1
        ) m ON p.id = m.pet_id
        WHERE af.user_id = p_user_id 
        AND af.status = 'approved';

    RETURN v_result;
END;
/