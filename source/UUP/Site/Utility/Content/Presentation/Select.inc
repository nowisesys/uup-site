<script>
        function select_on_change(select) {
            if(select.selectedIndex === -1) {
                return false;
            } else {
                document.location = select.options[select.selectedIndex].value;
            }
        }
</script>

<select onchange="return select_on_change(this)">
    <option>Go to location...</option>
    <?php foreach ($this->_explore->getFiles() as $spec => $data) : ?>
            <option value="<?= dirname($spec) ?>"><?= $data['name'] ?></option>
    <?php endforeach; ?>
</select>
