CREATE OR REPLACE TRIGGER create_adoption_record
AFTER INSERT ON adoption_form
FOR EACH ROW
BEGIN
    INSERT INTO adoptions (
        pet_id,
        adopter_id,
        adoption_date,
        status
    ) VALUES (
        :NEW.pet_id,
        :NEW.user_id,
        SYSDATE,
        'pending'
    );
END;
/

CREATE OR REPLACE TRIGGER update_adoption_status
AFTER UPDATE OF status ON adoption_form
FOR EACH ROW
BEGIN
    UPDATE adoptions
    SET status = 
        CASE 
            WHEN :NEW.status = 'approved' THEN 'approved'
            WHEN :NEW.status = 'rejected' THEN 'rejection'
            ELSE 'pending'
        END
    WHERE pet_id = :NEW.pet_id 
    AND adopter_id = :NEW.user_id;
END;
/ 