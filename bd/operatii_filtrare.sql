CREATE OR REPLACE FUNCTION get_animals_by_type(p_type IN VARCHAR2) 
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT *
        FROM pets
        WHERE LOWER(species) = LOWER(p_type);
    
    RETURN v_result;
END;
/
CREATE OR REPLACE FUNCTION get_animals_by_gender(p_gender IN VARCHAR2)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT *
        FROM pets
        WHERE LOWER(gender) = LOWER(p_gender);
    
    RETURN v_result;
END;
/
CREATE OR REPLACE FUNCTION get_animals_by_age_category(p_age_category IN VARCHAR2)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT *
        FROM pets
        WHERE 
            (LOWER(p_age_category) = 'young' AND age < 2)
            OR (LOWER(p_age_category) = 'adult' AND age BETWEEN 2 AND 7)
            OR (LOWER(p_age_category) = 'senior' AND age >= 8)
            OR p_age_category IS NULL;
    
    RETURN v_result;
END;
/
CREATE OR REPLACE FUNCTION get_animals_by_size(p_size IN VARCHAR2)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT *
        FROM pets
        WHERE LOWER(breed) LIKE '%' || LOWER(p_size) || '%'
           OR LOWER(description) LIKE '%' || LOWER(p_size) || '%';
    
    RETURN v_result;
END;
/
CREATE OR REPLACE FUNCTION filter_animals(
    p_type IN VARCHAR2,
    p_gender IN VARCHAR2,
    p_age_category IN VARCHAR2,
    p_size IN VARCHAR2
)
RETURN SYS_REFCURSOR
IS
    v_result SYS_REFCURSOR;
BEGIN
    OPEN v_result FOR
        SELECT *
        FROM pets
        WHERE (p_type IS NULL OR LOWER(species) = LOWER(p_type))
        AND (p_gender IS NULL OR LOWER(gender) = LOWER(p_gender))
        AND (p_age_category IS NULL OR 
            (CASE 
                WHEN p_age_category = 'young' AND age < 2 THEN 1
                WHEN p_age_category = 'adult' AND age BETWEEN 2 AND 7 THEN 1
                WHEN p_age_category = 'senior' AND age >= 8 THEN 1
                WHEN p_age_category IS NULL THEN 1
                ELSE 0
            END) = 1)
        AND (p_size IS NULL OR 1=1);
    
    RETURN v_result;
END;
/
