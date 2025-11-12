<!doctype html>
<html>
<body>
<h3>Import failed</h3>
<p>Your import (ID: {{ $import->id }}, type: {{ $import->import_type }}) has failed.</p>
<p>Message: {{ $import->message }}</p>
<p>Open the Imports tab to see validation errors and audits.</p>
</body>
</html>
