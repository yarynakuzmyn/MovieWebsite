<form action="" method="GET" class="bg-light p-4 rounded shadow-sm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="year" class="form-label">Рік:</label>
                                <select name="year" id="year" class="form-select">
                                    <option value="">Всі роки</option>
                                    <?php
                                    foreach ($years as $year) {
                                        $selected = ($_GET['year'] == $year['year']) ? 'selected' : '';
                                        echo "<option value='{$year['year']}' $selected>{$year['year']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="genre" class="form-label">Жанр:</label>
                                <select name="genre" id="genre" class="form-select">
                                    <option value="">Всі жанри</option>
                                    <?php
                                    foreach ($genres as $genre) {
                                        $selected = ($_GET['genre'] == $genre['name']) ? 'selected' : '';
                                        echo "<option value='{$genre['name']}' $selected>{$genre['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="country" class="form-label">Країна:</label>
                                <select name="country" id="country" class="form-select">
                                    <option value="">Всі країни</option>
                                    <?php
                                    foreach ($countries as $country) {
                                        $selected = ($_GET['country'] == $country['name']) ? 'selected' : '';
                                        echo "<option value='{$country['name']}' $selected>{$country['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="search" class="form-label">Пошук:</label>
                                <input type="text" name="search" id="search" class="form-control"
                                    placeholder="Назва фільму" value="<?php echo ($_GET['search'] ?? ''); ?>">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Пошук</button>
                            </div>
                        </div>
                    </form>