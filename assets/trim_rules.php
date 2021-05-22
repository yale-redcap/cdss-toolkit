<?php

/*
 * TRIM rule names
 * V1, 2021-02-18
 */

$cdss_medications = <<<EOJSON
[
   {"id": 1, "name": "ALENDRONATE", "label": "", "code": "", "comments": ""},
   {"id": 2, "name": "ALLOPURINOL", "label": "", "code": "", "comments": ""},
   {"id": 3, "name": "ALPRAZOLAM", "label": "", "code": "", "comments": ""},
   {"id": 4, "name": "AMANTADINE", "label": "", "code": "", "comments": ""},
   {"id": 5, "name": "AMITRIPTYLINE", "label": "", "code": "", "comments": ""},
   {"id": 6, "name": "ASPIRIN", "label": "", "code": "", "comments": ""},
   {"id": 7, "name": "ATENOLOL", "label": "", "code": "", "comments": ""},
   {"id": 8, "name": "CARVEDILOL", "label": "", "code": "", "comments": ""},
   {"id": 9, "name": "CHLORDIAZEPOXIDE", "label": "", "code": "", "comments": ""},
   {"id": 10, "name": "CHLORPHENIRAMINE", "label": "", "code": "", "comments": ""},
   {"id": 11, "name": "CHLORPROPRAMIDE", "label": "", "code": "", "comments": ""},
   {"id": 12, "name": "CITALOPRAM ", "label": "", "code": "", "comments": ""},
   {"id": 13, "name": "CLONAZEPAM", "label": "", "code": "", "comments": ""},
   {"id": 14, "name": "CLOPIDOGREL", "label": "", "code": "", "comments": ""},
   {"id": 15, "name": "CODEINE", "label": "", "code": "", "comments": ""},
   {"id": 16, "name": "CYCLOBENZAPRINE", "label": "", "code": "", "comments": ""},
   {"id": 17, "name": "DESIPRAMINE", "label": "", "code": "", "comments": ""},
   {"id": 18, "name": "DIAZEPAM", "label": "", "code": "", "comments": ""},
   {"id": 19, "name": "DICLOFENAC", "label": "", "code": "", "comments": ""},
   {"id": 20, "name": "DIGOXIN", "label": "", "code": "", "comments": ""},
   {"id": 21, "name": "DILTIAZEM", "label": "", "code": "", "comments": ""},
   {"id": 22, "name": "DIMENHYDRINATE", "label": "", "code": "", "comments": ""},
   {"id": 23, "name": "DIPHENHYDRAMINE", "label": "", "code": "", "comments": ""},
   {"id": 24, "name": "DOXYLAMINE", "label": "", "code": "", "comments": ""},
   {"id": 25, "name": "ESOMEPRAZOLE", "label": "", "code": "", "comments": ""},
   {"id": 26, "name": "ESZOPICLONE", "label": "", "code": "", "comments": ""},
   {"id": 27, "name": "ETODOLAC", "label": "", "code": "", "comments": ""},
   {"id": 28, "name": "FAMOTIDINE", "label": "", "code": "", "comments": ""},
   {"id": 29, "name": "FENTANYL", "label": "", "code": "", "comments": ""},
   {"id": 30, "name": "GABAPENTIN ", "label": "", "code": "", "comments": ""},
   {"id": 31, "name": "GALANTAMINE", "label": "", "code": "", "comments": ""},
   {"id": 32, "name": "GLYBURIDE", "label": "", "code": "", "comments": ""},
   {"id": 33, "name": "HYDROCHLOROTHIAZIDE", "label": "", "code": "", "comments": ""},
   {"id": 34, "name": "HYDROCODONE", "label": "", "code": "", "comments": ""},
   {"id": 35, "name": "HYDROMORPHONE", "label": "", "code": "", "comments": ""},
   {"id": 36, "name": "HYDROXYZINE", "label": "", "code": "", "comments": ""},
   {"id": 37, "name": "IBUPROFEN", "label": "", "code": "", "comments": ""},
   {"id": 38, "name": "IMIPRAMINE", "label": "", "code": "", "comments": ""},
   {"id": 39, "name": "INDOMETHACIN", "label": "", "code": "", "comments": ""},
   {"id": 40, "name": "LABETALOL", "label": "", "code": "", "comments": ""},
   {"id": 41, "name": "LANSOPRAZOLE", "label": "", "code": "", "comments": ""},
   {"id": 42, "name": "LORAZEPAM", "label": "", "code": "", "comments": ""},
   {"id": 43, "name": "MELOXICAM", "label": "", "code": "", "comments": ""},
   {"id": 44, "name": "MEMANTINE", "label": "", "code": "", "comments": ""},
   {"id": 45, "name": "METFORMIN", "label": "", "code": "", "comments": ""},
   {"id": 46, "name": "METHADONE", "label": "", "code": "", "comments": ""},
   {"id": 47, "name": "METHOCARBAMOL", "label": "", "code": "", "comments": ""},
   {"id": 48, "name": "METOCLOPRAMIDE", "label": "", "code": "", "comments": ""},
   {"id": 49, "name": "METOPROLOL", "label": "", "code": "", "comments": ""},
   {"id": 50, "name": "MORPHINE", "label": "", "code": "", "comments": ""},
   {"id": 51, "name": "NABUMETONE", "label": "", "code": "", "comments": ""},
   {"id": 52, "name": "NAPROXEN", "label": "", "code": "", "comments": ""},
   {"id": 53, "name": "NORTRIPTYLINE", "label": "", "code": "", "comments": ""},
   {"id": 54, "name": "OMEPRAZOLE", "label": "", "code": "", "comments": ""},
   {"id": 55, "name": "OXAZEPAM", "label": "", "code": "", "comments": ""},
   {"id": 56, "name": "OXYBUTYNIN", "label": "", "code": "", "comments": ""},
   {"id": 57, "name": "OXYCODONE", "label": "", "code": "", "comments": ""},
   {"id": 58, "name": "PANTOPRAZOLE", "label": "", "code": "", "comments": ""},
   {"id": 59, "name": "PIROXICAM", "label": "", "code": "", "comments": ""},
   {"id": 60, "name": "PROCHLORPERAZINE", "label": "", "code": "", "comments": ""},
   {"id": 61, "name": "PROMETHAZINE", "label": "", "code": "", "comments": ""},
   {"id": 62, "name": "PROPRANOLOL", "label": "", "code": "", "comments": ""},
   {"id": 63, "name": "RABEPRAZOLE", "label": "", "code": "", "comments": ""},
   {"id": 64, "name": "RANITIDINE", "label": "", "code": "", "comments": ""},
   {"id": 65, "name": "ROSUVASTATIN", "label": "", "code": "", "comments": ""},
   {"id": 66, "name": "SPIRONOLACTONE", "label": "", "code": "", "comments": ""},
   {"id": 67, "name": "SULINDAC", "label": "", "code": "", "comments": ""},
   {"id": 68, "name": "TEMAZEPAM", "label": "", "code": "", "comments": ""},
   {"id": 69, "name": "TOLTERODINE", "label": "", "code": "", "comments": ""},
   {"id": 70, "name": "TRAMADOL", "label": "", "code": "", "comments": ""},
   {"id": 71, "name": "TRIAMTERENE", "label": "", "code": "", "comments": ""},
   {"id": 72, "name": "TROSPIUM", "label": "", "code": "", "comments": ""},
   {"id": 73, "name": "VERAPAMIL", "label": "", "code": "", "comments": ""},
   {"id": 74, "name": "WARFARIN", "label": "", "code": "", "comments": ""},
   {"id": 75, "name": "ZALEPLON", "label": "", "code": "", "comments": ""},
   {"id": 76, "name": "ZOLPIDEM", "label": "", "code": "", "comments": ""}
]
EOJSON;

$cdss_diseases = <<<EOJSON
[
   {"id": 1, "name": "ATRIAL FIBRILLATION", "label": "", "code": "427", "comments": ""},
   {"id": 2, "name": "BPH/HISTORY OF URINARY RETENTION", "label": "", "code": "600,788.2", "comments": ""},
   {"id": 3, "name": "CAD OR MI", "label": "", "code": "410,411,412,413,414", "comments": ""},
   {"id": 4, "name": "CANCER", "label": "", "code": "140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,174,175,176,177,178,179,180,181,182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199,200,201,202,203,204,205,206,207,208,209,230,231,232,233,234,235,236,237,238,239", "comments": ""},
   {"id": 5, "name": "CHRONIC KIDNEY DISEASE", "label": "", "code": "585", "comments": ""},
   {"id": 6, "name": "COPD", "label": "", "code": "491,492,496", "comments": ""},
   {"id": 7, "name": "DEMENTIA", "label": "", "code": "290,294.1,294.2,331", "comments": ""},
   {"id": 8, "name": "DIABETES", "label": "", "code": "249,250", "comments": ""},
   {"id": 9, "name": "FALLS", "label": "", "code": "v15.88", "comments": ""},
   {"id": 10, "name": "GI BLEED", "label": "", "code": "578", "comments": ""},
   {"id": 11, "name": "CHF", "label": "", "code": "428", "comments": ""},
   {"id": 12, "name": "HYPERTENSION", "label": "", "code": "401,402,403,404", "comments": ""},
   {"id": 13, "name": "PARKINSONISM", "label": "", "code": "332", "comments": ""},
   {"id": 14, "name": "PEPTIC ULCER DISEASE", "label": "", "code": "533,v12.71", "comments": ""},
   {"id": 15, "name": "PERIPHERAL ARTERY DISEASE", "label": "", "code": "443", "comments": ""},
   {"id": 16, "name": "STROKE", "label": "", "code": "430,431,432,434,435", "comments": ""}
]
EOJSON;

$cdss_conditions = <<<EOJSON
[
   {"id": 1, "name": "CRCL<9", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<9", "comments": ""},
   {"id": 1, "name": "CRCL<20", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<20", "comments": ""},
   {"id": 1, "name": "CRCL<30", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<30", "comments": ""},
   {"id": 1, "name": "CRCL<35", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<35", "comments": ""},
   {"id": 1, "name": "CRCL<40", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<40", "comments": ""},
   {"id": 1, "name": "CRCL<50", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<50", "comments": ""},
   {"id": 1, "name": "CRCL<60", "label": "", "code": "[ehr_creatinine_clearance]>0 and [ehr_creatinine_clearance]<60", "comments": ""},
   {"id": 1, "name": "DIZZINESS", "label": "", "code": "[hs_dizziness]=1", "comments": ""},
   {"id": 1, "name": "HYPERTENSION_MEDICATIONS", "label": "", "code": "[meds_hypertension]=1", "comments": ""},
   {"id": 1, "name": "DIABETES_MEDICATIONS", "label": "", "code": "[meds_diabetes]=1", "comments": ""},
   {"id": 1, "name": "EXECUTIVE_DYSFUNCTION", "label": "", "code": "[hs_trails_errors]>2", "comments": ""},
   {"id": 1, "name": "LOW_ADHERENCE", "label": "", "code": "[meds_adherence]=1", "comments": ""},
   {"id": 1, "name": "YES_CURRENT_SUPPORT", "label": "", "code": "[ms_doesanyonehelp]=1", "comments": ""},
   {"id": 1, "name": "NO_CURRENT_SUPPORT", "label": "", "code": "[ms_doesanyonehelp]=2", "comments": ""},
   {"id": 1, "name": "YES_FUTURE_SUPPORT", "label": "", "code": "[ms_cananyonehelp]=1", "comments": ""},
   {"id": 1, "name": "NO_FUTURE_SUPPORT", "label": "", "code": "[ms_cananyonehelp]=2", "comments": ""},
   {"id": 1, "name": "CONSTIPATION", "label": "", "code": "[hs_constipation]=1", "comments": ""},
   {"id": 1, "name": "AGE>75", "label": "", "code": "[ehr_age]>75", "comments": ""},
   {"id": 1, "name": "AGE>80", "label": "", "code": "[ehr_age]>80", "comments": ""},
   {"id": 1, "name": "FUNCTIONAL_DISABILITY", "label": "", "code": "[calc_functional_disability]", "comments": ""},
   {"id": 1, "name": "FALLS", "label": "", "code": "[hs_falls]>1 or [calc_falls]=1", "comments": ""},
   {"id": 1, "name": "DIGOXIN>125", "label": "", "code": "medication(DIGOXIN)>125", "comments": ""},
   {"id": 1, "name": "GFR<30", "label": "", "code": "[ehr_egfr]>0 and [ehr_egfr]<30", "comments": ""},
   {"id": 1, "name": "GFR<45", "label": "", "code": "[ehr_egfr]>0 and [ehr_egfr]<45", "comments": ""},
   {"id": 1, "name": "GFR<50", "label": "", "code": "[ehr_egfr]>0 and [ehr_egfr]<50", "comments": ""},
   {"id": 1, "name": "GFR<60", "label": "", "code": "[ehr_egfr]>0 and [ehr_egfr]<60", "comments": ""},
   {"id": 1, "name": "HGBA1C<8", "label": "", "code": "[ehr_hgba1c]>0 and [ehr_hgba1c]<8", "comments": ""},
   {"id": 1, "name": "HGBA1C<7.5", "label": "", "code": "[ehr_hgba1c]>0 and [ehr_hgba1c]<7.5", "comments": ""},
   {"id": 1, "name": "HGBA1C<7", "label": "", "code": "[ehr_hgba1c]>0 and [ehr_hgba1c]<7", "comments": ""},
   {"id": 1, "name": "5-YEAR_LIFE<50", "label": "", "code": "[calc_5year50]>=16", "comments": ""},
   {"id": 1, "name": "SBP<130", "label": "", "code": "[ehr_systbp]>0 and [ehr_systbp]<130", "comments": ""},
   {"id": 1, "name": "SBP<140", "label": "", "code": "[ehr_systbp]>0 and [ehr_systbp]<140", "comments": ""},
   {"id": 1, "name": "SBP<150", "label": "", "code": "[ehr_systbp]>0 and [ehr_systbp]<150", "comments": ""},
   {"id": 1, "name": "SBP<85", "label": "", "code": "[ehr_diasbp]>0 and [ehr_diasbp]<85", "comments": ""},
   {"id": 1, "name": "SBP<75", "label": "", "code": "[ehr_diasbp]>0 and [ehr_diasbp]<75", "comments": ""},
   {"id": 1, "name": "COMORBS", "label": "", "code": "[calc_comorbs]>1", "comments": ""}
]
EOJSON;

$cdss_variables = <<<EOJSON
[
   {
      "name": "calc_functional_disability",
      "label": "Calculated (0, 1) functional disability score: 1=any ADL deficit",
      "code": "[adl_1]=2 or [adl_1]=3 or [adl_2]=2 or [adl_2]=3 or [adl_3]=2 or [adl_3]=3 or [adl_4]=2 or [adl_4]=3 or [adl_5]=2 or [adl_5]=3 or [adl_6]=2 or [adl_6]=3 or [adl_7]=2 or [adl_7]=3",
      "comments":""
   },
   {
      "name": "calc_comorbs",
      "label": "Count of comorbid diagnoses", 
      "code": "disease(CHRONIC KIDNEY DISEASE) + disease(COPD) + disease(CANCER) + disease(HYPERTENSION) + disease(CHF) + disease(CAD OR MI) + disease(PERIPHERAL ARTERY DISEASE) + disease(STROKE)", 
      "comments":""
   },
   {
      "name": "calc_falls",
      "label": "Calculated (0, 1) falls indicator: 1=diagnosis or self-reported history", 
      "code": "disease(FALLS) or [hs_falls] > 1", 
      "comments":""
   },
   {
      "name": "calc_5year50",
      "label": "Calculated 5-year mortality risk", 
      "code": "1 * ( [ehr_age] >= 70 and [ehr_age] < 75 ) + 3 * ( [ehr_age] >= 75 and [ehr_age] < 80 ) + 5 * ( [ehr_age] >= 80 and [ehr_age] < 85 ) + 7 * ( [ehr_age] >= 85 ) + 3 * ( [pt_sex] = 'male' ) + 1 * ( [hs_cigarettes] = 2 ) + 3 * ( [hs_cigarettes] = 3 ) + 2 * ( [ehr_bmi] > 0 and [ehr_bmi] < 25 ) + 2 * disease(DIABETES) + 2 * disease(COPD) + 2 * disease(CANCER) + 1 * ( [hs_hospital] = 2 ) + 3 * ( [hs_hospital] = 3 ) + 1 * ( [hs_ratehealth] = 2 ) + 2 * ( [hs_ratehealth] = 3 ) + 2 * ( [hs_one_iadl] = 1 ) + 3 * ( [hs_walk] = 2 or [hs_walk] = 3 ) + 1 * [calc_functional_disability]",
      "comments":""
   }
]
EOJSON;

