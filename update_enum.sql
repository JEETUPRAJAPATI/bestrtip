ALTER TABLE property_booking MODIFY status ENUM('Enquiry', 'Hold', 'Confirmed', 'Cancel') DEFAULT 'Confirmed';
