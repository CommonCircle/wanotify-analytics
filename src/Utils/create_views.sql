CREATE OR REPLACE view encv_issuance_method_data_daily AS
SELECT
    encv_code_data_daily.date,
    JSON_INSERT(
        encv_api_key_data_daily.data,
            '$.codes_issued', JSON_VALUE(encv_code_data_daily.data, '$.codes_issued'),
            '$.user_reports_issued', JSON_VALUE(encv_code_data_daily.data, '$.user_reports_issued'),
            '$.Total_User_Issuance', JSON_VALUE(encv_user_data_daily.data, '$.Total_User_Issuance'),
            '$.Max_User_Issuance', JSON_VALUE(encv_user_data_daily.data, '$.Max_User_Issuance')
    ) AS data
FROM encv_code_data_daily 
JOIN encv_api_key_data_daily ON encv_code_data_daily.date = encv_api_key_data_daily.date
JOIN encv_user_data_daily ON encv_code_data_daily.date = encv_user_data_daily.date;