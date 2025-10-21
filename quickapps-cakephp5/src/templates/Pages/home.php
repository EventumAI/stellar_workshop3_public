<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vacation Calculator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        input[type="date"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="date"]:focus,
        input[type="number"]:focus {
            outline: none;
            border-color: #3498db;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #2980b9;
        }

        .result {
            margin-top: 30px;
            padding: 20px;
            background: #ecf0f1;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }

        .result h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .result-item {
            padding: 8px 0;
            border-bottom: 1px solid #bdc3c7;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-label {
            color: #7f8c8d;
            font-size: 14px;
        }

        .result-value {
            color: #2c3e50;
            font-weight: 500;
            font-size: 16px;
        }

        .result-total {
            margin-top: 15px;
            padding: 15px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            text-align: center;
        }

        .result-total .label {
            font-size: 14px;
            opacity: 0.9;
        }

        .result-total .value {
            font-size: 36px;
            font-weight: bold;
            margin-top: 5px;
        }

        .errors {
            background: #e74c3c;
            color: white;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .errors ul {
            list-style: none;
        }

        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Vacation Calculator</h1>
        <p class="subtitle">Calculate available vacation days including seniority bonus</p>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/">
            <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>
            <div class="form-group">
                <label for="hire_date">Hire Date *</label>
                <input
                    type="date"
                    id="hire_date"
                    name="hire_date"
                    required
                    value="<?= $this->request->getData('hire_date') ?? '' ?>"
                >
                <p class="help-text">The date when the employee started working at the company</p>
            </div>

            <div class="form-group">
                <label for="base_days">Base Vacation Days Per Year *</label>
                <input
                    type="number"
                    id="base_days"
                    name="base_days"
                    min="1"
                    max="365"
                    required
                    value="<?= $this->request->getData('base_days') ?? '20' ?>"
                >
                <p class="help-text">Number of vacation days allocated for a full year of work (typically 20-28 days)</p>
            </div>

            <div class="form-group">
                <label for="calculation_date">Calculation Date</label>
                <input
                    type="date"
                    id="calculation_date"
                    name="calculation_date"
                    value="<?= $this->request->getData('calculation_date') ?? date('Y-m-d') ?>"
                >
                <p class="help-text">Date for which to calculate vacation days (defaults to today)</p>
            </div>

            <button type="submit">Calculate</button>
        </form>

        <?php if (isset($result) && $result): ?>
            <div class="result">
                <h2>Calculation Result</h2>

                <div class="result-item">
                    <div class="result-label">Employment Period</div>
                    <div class="result-value">
                        from <?= h($result['hire_date']) ?> to <?= h($result['calculation_date']) ?>
                    </div>
                </div>

                <div class="result-item">
                    <div class="result-label">Length of Service</div>
                    <div class="result-value">
                        <?= h($result['years_worked']) ?>
                        <?= $result['years_worked'] == 1 ? 'year' : 'years' ?>
                        <?php if ($result['months_worked'] > 0): ?>
                            and <?= h($result['months_worked']) ?>
                            <?= $result['months_worked'] == 1 ? 'month' : 'months' ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="result-item">
                    <div class="result-label">Base Vacation Days</div>
                    <div class="result-value">
                        <?= h($result['base_days']) ?> days
                        <small style="color: #7f8c8d;">(of <?= h($result['base_days_per_year']) ?> annual)</small>
                    </div>
                </div>

                <div class="result-item">
                    <div class="result-label">Seniority Bonus</div>
                    <div class="result-value">
                        +<?= h($result['seniority_bonus']) ?> days
                        <?php if ($result['seniority_bonus'] > 0): ?>
                            <small style="color: #7f8c8d;">(+5 days every 5 years)</small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="result-total">
                    <div class="label">Total Available Vacation Days</div>
                    <div class="value"><?= h($result['total_days']) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
