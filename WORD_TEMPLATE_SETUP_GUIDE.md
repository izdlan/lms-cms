# 📄 Microsoft Word Certificate Template Setup Guide

## 🎯 **Step-by-Step Template Creation**

### **Step 1: Open Your Existing Template**
1. Open the file: `C:\xampp\htdocs\lms-cms\template sijil lama.docx`
2. Save it as: `certificate_template.docx`
3. Copy it to: `C:\xampp\htdocs\lms-cms\storage\app\templates\certificate_template.docx`

### **Step 2: Replace Static Text with Placeholders**

#### **Find and Replace These Texts:**

| **Original Text** | **Replace With** | **Font** | **Size** | **Purpose** |
|-------------------|------------------|----------|----------|-------------|
| `Yang Ling` | `{{STUDENT_NAME}}` | **Vivaldi** | 18pt | Student's name |
| `Bachelor of Science (Hons) in Information & Communication Technology` | `{{COURSE_NAME}}` | **Vivaldi** | 14pt | Course/Program name |
| `Tenth day of June 2019` | `{{GRADUATION_DATE}}` | **Arial** | 11pt | Graduation date |
| `201600001036` | `{{CERTIFICATE_NUMBER}}` | **Copperplate Gothic Bold** | 9pt | Certificate number |

### **Step 3: Apply Font Formatting**

#### **Format Each Placeholder with Correct Font:**

1. **Student Name (`{{STUDENT_NAME}}`)**:
   - Select the text `{{STUDENT_NAME}}`
   - Font: **Vivaldi**
   - Size: **18pt**
   - Alignment: **Center**

2. **Course Name (`{{COURSE_NAME}}`)**:
   - Select the text `{{COURSE_NAME}}`
   - Font: **Vivaldi**
   - Size: **14pt**
   - Alignment: **Center**

3. **Graduation Date (`{{GRADUATION_DATE}}`)**:
   - Select the text `{{GRADUATION_DATE}}`
   - Font: **Arial**
   - Size: **11pt**
   - Alignment: **Center**

4. **Certificate Number (`{{CERTIFICATE_NUMBER}}`)**:
   - Select the text `{{CERTIFICATE_NUMBER}}`
   - Font: **Copperplate Gothic Bold**
   - Size: **9pt**
   - Style: **Bold**
   - Alignment: **Center**

#### **Additional Formatting Tips:**
- **Line Spacing**: Set to "Single" or "1.0" for all placeholders
- **Character Spacing**: Normal (0pt) for all text
- **Paragraph Spacing**: 0pt before and after for placeholders
- **Text Wrapping**: Set to "In line with text" for better control

### **Step 4: Add QR Code Placeholder**

#### **Method 1: Using Image Placeholder**
1. **Position**: Below the certificate number (201600001036)
2. **Size**: 2cm x 2cm (or as needed)
3. **Steps**:
   - Go to **Insert** → **Shapes** → **Rectangle**
   - Draw a rectangle where you want the QR code
   - Right-click the rectangle → **Format Shape**
   - Set **Width**: 2cm, **Height**: 2cm
   - Add text inside: `[QR_CODE_HERE]`
   - Set text alignment to center

#### **Method 2: Using Text Placeholder**
1. **Position**: Below the certificate number
2. **Add text**: `{{QR_CODE}}`
3. **Format**: Center aligned, bold

### **Step 5: Template Layout Structure**

```
┌─────────────────────────────────────────────────────────┐
│                    [OLYMPIA COLLEGE LOGO]               │
│                    OLYMPIA COLLEGE MALAYSIA             │
├─────────────────────────────────────────────────────────┤
│                                                         │
│                    Bachelor of Science                  │
│                                                         │
│              This is to certify that                    │
│                                                         │
│                    {{STUDENT_NAME}}                     │
│                                                         │
│              has been awarded the                       │
│                                                         │
│        {{COURSE_NAME}}                                  │
│                                                         │
│    having fulfilled the requirements prescribed by      │
│    the Academic Board, and with the assent of the      │
│    Examination Board. Witness our hand and seal this   │
│                                                         │
│                {{GRADUATION_DATE}}                      │
│                                                         │
├─────────────────────────────────────────────────────────┤
│  [Director]        [Chairman]        [Registrar]       │
│  Director          Chairman          Registrar         │
│  Olympia College   Olympia Academic  Olympia Exam      │
│                    Board             Board              │
│                                                         │
│  [RED SEAL]       Certificate No: {{CERTIFICATE_NUMBER}} │
│                   [QR_CODE_HERE]                        │
└─────────────────────────────────────────────────────────┘
```

### **Step 6: Font and Styling Guidelines**

#### **Font Settings:**
- **Main Title**: Times New Roman, 24pt, Bold, Center
- **Student Name**: **Vivaldi**, 18pt, Center
- **Course Name**: **Vivaldi**, 14pt, Center
- **Body Text**: Times New Roman, 12pt, Regular
- **Graduation Date**: **Arial**, 11pt, Center
- **Certificate Number**: **Copperplate Gothic Bold**, 9pt, Bold
- **QR Code Placeholder**: Arial, 8pt, Center

#### **Layout Settings:**
- **Page Size**: A4 (210mm x 297mm)
- **Margins**: 2cm on all sides
- **Orientation**: Portrait

### **Step 7: Save and Test**

1. **Save the template** as `certificate_template.docx`
2. **Copy to Laravel storage**:
   ```
   Copy: C:\xampp\htdocs\lms-cms\template sijil lama.docx
   To: C:\xampp\htdocs\lms-cms\storage\app\templates\certificate_template.docx
   ```

3. **Test the template** by visiting:
   ```
   http://127.0.0.1:8000/certificates/generate/1
   ```
   (Replace `1` with an actual student ID)

## 🔧 **Laravel Integration Details**

### **QR Code Content:**
The QR code will contain this JSON data:
```json
{
    "student_name": "Student Name",
    "certificate_number": "CERT-2025-000001",
    "course": "Bachelor of Science (Hons) in ICT",
    "graduation_date": "10 June 2019",
    "verification_url": "https://lms.olympia-education.com/verify-certificate/CERT-2025-000001",
    "generated_at": "2025-10-14T10:21:42+02:00"
}
```

### **Available Routes:**
- **Generate Certificate**: `/certificates/generate/{studentId}`
- **Download Certificate**: `/certificates/download/{studentId}`
- **Verify Certificate**: `/certificates/verify/{certificateNumber}`
- **List Certificates**: `/certificates/`

### **Database Fields Added:**
- `certificate_number` - Unique certificate identifier
- `graduation_date` - Date of graduation
- `certificate_generated` - Boolean flag
- `certificate_generated_at` - Timestamp of generation

## 🎨 **Template Customization Tips**

### **Professional Look:**
1. **Use consistent fonts** throughout the document
2. **Maintain proper spacing** between sections
3. **Keep the layout balanced** and centered
4. **Use high-quality images** for logos and seals

### **QR Code Positioning:**
1. **Place below certificate number** for easy scanning
2. **Make it large enough** to scan easily (minimum 2cm x 2cm)
3. **Ensure good contrast** with background
4. **Test with QR code scanner** after generation

### **Color Scheme:**
- **Primary**: Dark blue or black for text
- **Accent**: Red for seals and important elements
- **Background**: White or cream for professional look

## 🚀 **Testing Your Template**

### **Test Steps:**
1. **Create a test student** in your database
2. **Generate a certificate** using the route
3. **Check all placeholders** are replaced correctly
4. **Verify QR code** is generated and positioned properly
5. **Test certificate download** functionality

### **Common Issues:**
- **Placeholders not replaced**: Check spelling and case sensitivity
- **QR code not appearing**: Ensure image placeholder is properly set
- **Formatting issues**: Check font sizes and alignment
- **File not found**: Verify template is in correct location

## 📋 **Final Checklist**

- [ ] Template saved as `certificate_template.docx`
- [ ] Template copied to `storage/app/templates/`
- [ ] All placeholders replaced with `{{PLACEHOLDER}}` format
- [ ] QR code placeholder added
- [ ] Fonts and styling applied
- [ ] Template tested with sample data
- [ ] Database migration run successfully
- [ ] Routes working correctly

## 🎉 **You're Ready!**

Once you complete these steps, your certificate generation system will be fully functional. Students can have their certificates generated with their specific data and QR codes for verification!
