$ErrorActionPreference = 'Stop'

$latin1 = [System.Text.Encoding]::GetEncoding('ISO-8859-1')
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
$mojibakePattern = "[{0}{1}{2}{3}{4}{5}{6}{7}]" -f [char]0x00C3, [char]0x00C2, [char]0x00E4, [char]0x00E5, [char]0x00E6, [char]0x00E7, [char]0x00E8, [char]0x00E9
$traditional = -join ([char]0x7E41, [char]0x9AD4, [char]0x4E2D, [char]0x6587)
$simplified = -join ([char]0x7B80, [char]0x4F53, [char]0x4E2D, [char]0x6587)

function Write-Utf8NoBom([string]$path, [string]$text) {
    [System.IO.File]::WriteAllText((Resolve-Path $path), $text, $utf8NoBom)
}

function Repair-MojibakeFile([string]$path) {
    $text = [System.IO.File]::ReadAllText((Resolve-Path $path))

    for ($i = 0; $i -lt 4; $i++) {
        if ($text -notmatch $mojibakePattern) {
            break
        }

        $fixed = [System.Text.Encoding]::UTF8.GetString($latin1.GetBytes($text))

        if ($fixed -eq $text) {
            break
        }

        $text = $fixed
    }

    Write-Utf8NoBom $path $text
}

function Replace-LanguageBlock([string]$path) {
    $text = [System.IO.File]::ReadAllText((Resolve-Path $path))

    $replacement = @"
@if (app()->getLocale() == 'en')
                        English
                    @elseif (app()->getLocale() == 'zh')
                        $traditional
                    @else
                        $simplified
                    @endif
"@

    $text = [regex]::Replace($text, "@if \(app\(\)->getLocale\(\) == 'en'\)[\s\S]*?@endif", $replacement, 1)

    Write-Utf8NoBom $path $text
}

function Replace-DropdownLinks([string]$path) {
    $text = [System.IO.File]::ReadAllText((Resolve-Path $path))
    $text = $text.Replace("{{ url('lang/en') }}", "{{ language_switch_url('en') }}")
    $text = $text.Replace("{{ url('lang/zh') }}", "{{ language_switch_url('zh') }}")
    $text = $text.Replace("{{ url('lang/zh-CN') }}", "{{ language_switch_url('zh-CN') }}")

    $dropdownPattern = '<a class="dropdown-item" href="\{\{ (?:url|language_switch_url)\(''en''\) \}\}">[\s\S]*?</a>\s*<a class="dropdown-item" href="\{\{ (?:url|language_switch_url)\(''zh''\) \}\}">[\s\S]*?</a>\s*<a class="dropdown-item" href="\{\{ (?:url|language_switch_url)\(''zh-CN''\) \}\}">[\s\S]*?</a>'
    $dropdownReplacement = @"
<a class="dropdown-item" href="{{ language_switch_url('en') }}"><i class="fa fa-language" aria-hidden="true"></i>English</a>
                    <a class="dropdown-item" href="{{ language_switch_url('zh') }}"><i class="fa fa-language" aria-hidden="true"></i> $traditional</a>
                    <a class="dropdown-item" href="{{ language_switch_url('zh-CN') }}"><i class="fa fa-language" aria-hidden="true"></i> $simplified</a>
"@

    $text = [regex]::Replace($text, $dropdownPattern, $dropdownReplacement, 1)

    Write-Utf8NoBom $path $text
}

Repair-MojibakeFile 'resources/lang/zh/messages.php'
Repair-MojibakeFile 'resources/lang/zh-CN/messages.php'
Repair-MojibakeFile 'resources/lang/zh/left-navbar.php'
Repair-MojibakeFile 'resources/lang/zh/tour-package.php'

$languageFiles = @(
    'resources/views/frontend/layouts/navbar.blade.php',
    'resources/views/layouts/home/navbar.blade.php',
    'resources/views/component/menu.blade.php'
)

foreach ($path in $languageFiles) {
    Replace-LanguageBlock $path
    Replace-DropdownLinks $path
}

$reviewFiles = @(
    'resources/views/home/reviews/create.blade.php',
    'resources/views/home/reviews/create-review.blade.php',
    'resources/views/home/reviews/create-wedding-review.blade.php'
)

foreach ($path in $reviewFiles) {
    Replace-DropdownLinks $path
}
