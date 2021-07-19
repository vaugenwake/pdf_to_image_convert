# Convert PDF to Images

This is a quick exploration into converting PDF files into single seporated images.

### Required PHP extensions:
* Image Magic

### Dealing with the PDF security policy
To read/write PDF file with Image Magic you need to edit the policy.

1. Edit: `/etc/ImageMagic-6/policy.xml`
2. Replace: 
```XML
<policy domain="coder" rights="none" pattern="PDF" />
```
To Be:
```XML
<policy domain="coder" rights="read|write" pattern="PDF" />
```