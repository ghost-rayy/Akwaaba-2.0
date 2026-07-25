<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Letter</title>
    <style>
        body {
            font-family: 'DejaVu Serif', Georgia, serif;
            font-size: 12pt;
            line-height: 1.7;
            color: #111;
            margin: 60px 70px;
        }
        p { margin: 0 0 0.5em 0; }
        h1 { font-size: 20pt; margin: 0 0 0.5em 0; }
        h2 { font-size: 16pt; margin: 0 0 0.5em 0; }
        h3 { font-size: 14pt; margin: 0 0 0.5em 0; }
        ul, ol { margin: 0 0 0.5em 0; padding-left: 1.5em; }
        img { max-height: 80px; max-width: 200px; }

        /* Quill formatting classes */
        .ql-align-center { text-align: center; }
        .ql-align-right { text-align: right; }
        .ql-align-justify { text-align: justify; }
        .ql-indent-1 { padding-left: 3em; }
        .ql-indent-2 { padding-left: 6em; }
        .ql-indent-3 { padding-left: 9em; }
        .ql-indent-4 { padding-left: 12em; }
        .ql-indent-5 { padding-left: 15em; }
        .ql-size-small { font-size: 9pt; }
        .ql-size-large { font-size: 16pt; }
        .ql-size-huge { font-size: 22pt; }
        .ql-font-serif { font-family: 'DejaVu Serif', Georgia, serif; }
        .ql-font-monospace { font-family: 'DejaVu Sans Mono', monospace; }
        u { text-decoration: underline; }
        s { text-decoration: line-through; }
    </style>
</head>
<body>
    {!! $content !!}
</body>
</html>
