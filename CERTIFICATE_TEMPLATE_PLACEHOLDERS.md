# Certificate Template Placeholders Guide

## Overview
This guide explains the placeholders you should use in your certificate templates (both DOCX and PDF).

## Available Placeholders

### 1. Student Information
- **`${STUDENT_NAME}`** or **`STUDENT_NAME`**
  - Replaces with: Student's full name
  - Example: "KHALID BIN NORDIN"

### 2. Program Information

- **`${COURSE_NAME}`** or **`COURSE_NAME`**
  - Replaces with: **Short program name** (used for the large decorative text at top)
  - Example: "Bachelor of Science"
  - This is the abbreviated/short form of the program

- **`${PROGRAM_NAME}`** or **`PROGRAM_NAME`** or **`${FULL_PROGRAM_NAME}`** or **`FULL_PROGRAM_NAME`**
  - Replaces with: **Full program name** (used for the detailed program description in body)
  - Example: "Bachelor of Science (Hons) in Information & Communication Technology"
  - This is the complete, official program name

### 3. Graduation Information

- **`${GRADUATION_DATE}`** or **`GRADUATION_DATE`**
  - Replaces with: **Formatted graduation date in ordinal format**
  - Format: "Tenth day of June 2011"
  - Includes ordinal day (First, Second, Third, ... Tenth, Eleventh, etc.), month name, and year

### 4. Certificate Details

- **`${CERTIFICATE_NUMBER}`** or **`CERTIFICATE_NUMBER`**
  - Replaces with: Certificate number
  - Example: "CERT-20251105-0001"

- **`${STUDENT_ID}`** or **`STUDENT_ID`**
  - Replaces with: Student ID
  - Example: "UPBB8008"

### 5. QR Code

- **`${QR_CODE}`** or **`QR_CODE`**
  - Replaces with: QR code image for certificate verification
  - This is an image placeholder, not text

## Template Structure

### For DOCX Template:
Place these placeholders where you want the data to appear:

```
${COURSE_NAME}           ← Short program (e.g., "Bachelor of Science")
${STUDENT_NAME}          ← Student name
${PROGRAM_NAME}         ← Full program (e.g., "Bachelor of Science (Hons) in ICT")
${GRADUATION_DATE}      ← "Tenth day of June 2011"
${CERTIFICATE_NUMBER}   ← Certificate number
${STUDENT_ID}           ← Student ID
${QR_CODE}              ← QR code image
```

### For PDF Template:
The PDF template uses the same placeholders, but the system will:
1. Cover placeholder areas with white rectangles
2. Overlay the actual text/images on top

## Important Notes

1. **Program Names:**
   - `${COURSE_NAME}` = Short name (for decorative/display purposes)
   - `${PROGRAM_NAME}` = Full name (for official description)

2. **Date Format:**
   - The graduation date is automatically formatted as: "Tenth day of June 2011"
   - The system converts the day number to ordinal words (First, Second, Third, etc.)

3. **Placeholder Format:**
   - Both formats work: `${PLACEHOLDER}` and `PLACEHOLDER`
   - Use whichever format matches your template

4. **QR Code:**
   - This must be an image placeholder in your template
   - The system will replace it with the actual QR code image

## Example Template Layout

```
OLYMPIA COLLEGE MALAYSIA
[Logo/Crest]

${COURSE_NAME}                    ← Large decorative text
                                    (e.g., "Bachelor of Science")

This is to certify that

${STUDENT_NAME}                   ← Student name
                                    (e.g., "KHALID BIN NORDIN")

has been awarded the

${PROGRAM_NAME}                    ← Full program name in body
                                    (e.g., "Bachelor of Science (Hons) in Information & Communication Technology")

having fulfilled the requirements prescribed by the Academic Board...

Witness our hand and seal this

${GRADUATION_DATE}                 ← "Tenth day of June 2011"

[Signatures and Seal]

Certificate No: ${CERTIFICATE_NUMBER}
Student ID: ${STUDENT_ID}
${QR_CODE}                         ← QR code image (bottom right)
```

