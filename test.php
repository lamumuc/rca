<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Show/Hide Example</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="btn-group btn-group-toggle" data-toggle="buttons">
<label class="btn btn-sm btn-light active">
    <input type="radio" name="vOpts" id="viewMark" autocomplete="off" checked> Mark
</label>
<label class="btn btn-sm btn-light">
    <input type="radio" name="vOpts" id="viewCate" autocomplete="off" > Class
</label>
</div>
    


        

    <div id="markOptions" style="display: none;">
        Mark Options
    </div>

    <div id="cateOptions" style="display: none;">
        Class Options
    </div>

    <script>
        $(document).ready(function () {
            // Handle radio button change event
            $('input[name="vOpts"]').on('change', function () {
                if ($(this).attr('id') === 'viewMark') {
                    $('#markOptions').show();
                    $('#cateOptions').hide();
                } else if ($(this).attr('id') === 'viewCate') {
                    $('#markOptions').hide();
                    $('#cateOptions').show();
                }
            });
        });
    </script>
</body>
</html>



<script>

</script>