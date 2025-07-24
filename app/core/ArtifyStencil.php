<?php
// ArtifyStencil - Motor de Plantillas estilo Blade
namespace App\core;

class ArtifyStencil {

    protected $cachePath = __DIR__ . '/cache/';
    protected $viewPath = __DIR__ . '/../Views/';
    protected $sections = [];
    protected $sectionStack = [];

    public function render($templateFile, $data = []) {
        $templateFile = $this->viewPath . $templateFile . '.php';
        $compiledFile = $this->getCompiledPath($templateFile);

        if (!file_exists($compiledFile) || filemtime($templateFile) > filemtime($compiledFile)) {
            $this->compile($templateFile, $compiledFile);
        }

        extract($data);
        ob_start();
        include $compiledFile;
        return ob_get_clean();
    }

    protected function getCompiledPath($templateFile) {
        return $this->cachePath . md5($templateFile) . '.php';
    }

   protected function compile($templateFile, $compiledFile) {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }

        $content = file_get_contents($templateFile);

        // Sin escape: {!! $var !!}
        $content = preg_replace('/\{!!\s*(.*?)\s*!!\}/s', '<?php echo $1; ?>', $content);
        
        // Con escape: {{ $var }}
        // Aquí usaremos preg_replace_callback para que el código PHP quede bien
        $content = preg_replace_callback('/\{\{\s*(.*?)\s*\}\}/s', function ($matches) {
            return '<?php echo htmlspecialchars(' . $matches[1] . ', ENT_QUOTES, \'UTF-8\'); ?>';
        }, $content);

        $content = preg_replace_callback('/@if\s*\(([^)]+)\)/s', function ($matches) {
            return '<?php if (' . $matches[1] . '): ?>';
        }, $content);

        $content = preg_replace_callback('/@elseif\s*\((.+?)\)/s', function ($matches) {
            return '<?php elseif (' . $matches[1] . '): ?>';
        }, $content);

        $content = preg_replace('/@else/', '<?php else: ?>', $content);
        $content = preg_replace('/@endif/', '<?php endif; ?>', $content);

        // @foreach, @endforeach
        $content = preg_replace_callback('/@foreach\s*\((.*?)\)/s', function ($matches) {
            return '<?php foreach (' . $matches[1] . '): ?>';
        }, $content);

        $content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);

        // @include
        $content = preg_replace_callback('/@include\s*\(\s*[\'"](.+?)[\'"]\s*\)/', function ($matches) {
            $template = $matches[1];
            $templateFile = $this->viewPath . $template . '.php';
            $compiledFile = $this->getCompiledPath($templateFile);
            if (!file_exists($compiledFile) || filemtime($templateFile) > filemtime($compiledFile)) {
                $this->compile($templateFile, $compiledFile);
            }
            return "<?php include '$compiledFile'; ?>";
        }, $content);

        // @section and @endsection
        $content = preg_replace_callback('/@section\s*\(\s*[\'"](.*?)[\'"]\s*\)(.*?)@endsection/s', function ($matches) {
            $sectionName = $matches[1];
            $sectionContent = $matches[2];
            return "<?php \$this->sections['$sectionName'] = function() { ?>$sectionContent<?php }; ?>";
        }, $content);

        // @yield
        // Usamos preg_replace_callback para que la variable $1 se reemplace correctamente en PHP
        $content = preg_replace_callback('/@yield\s*\(\s*[\'"](.*?)[\'"]\s*\)/', function ($matches) {
            $sectionName = $matches[1];
            return "<?php if(isset(\$this->sections['$sectionName'])) { \$this->sections['$sectionName'](); } ?>";
        }, $content);

        // @extends
        if (preg_match('/@extends\s*\(\s*[\'"](.*?)[\'"]\s*\)/', $content, $matches)) {
            $layout = $matches[1];
            $layoutFile = $this->viewPath . $layout . '.php';
            $compiledLayout = $this->getCompiledPath($layoutFile);
            if (!file_exists($compiledLayout) || filemtime($layoutFile) > filemtime($compiledLayout)) {
                $this->compile($layoutFile, $compiledLayout);
            }

            $content = preg_replace('/@extends\s*\(\s*[\'"](.*?)[\'"]\s*\)/', '', $content);
            $content .= "\n<?php include '$compiledLayout'; ?>";
        }

        // Soporte a @php ... @endphp (multilínea)
        $content = preg_replace('/@php\s*(.*?)\s*@endphp/s', '<?php $1 ?>', $content);

        file_put_contents($compiledFile, $content);
    }
}
