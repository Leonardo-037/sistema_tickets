<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MakeMiddlewareCommand extends Command
{
    protected function configure()
    {
        $this->setName('make:middleware')
             ->setDescription('Crear un nuevo Middleware. ( php arco make:middleware NombreMiddleware )')
             ->addArgument('name', InputArgument::REQUIRED, 'Name of the middleware');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        // Ruta completa para el nuevo modelo
        $middlewarePath = __DIR__ . '/../../core/middleware/' . $name . 'Middleware.php';

        // Lógica para generar el contenido del modelo
        $middlewareContent = "<?php

        namespace App\core\middleware;

        use App\core\DB;
        use App\core\Redirect;
        
        class " . $name . "Middleware
        {
            public function handle(\$request, \$next)
            {
                // Tu lógica acá
                return \$next(\$request);
            }

        }";

        // Guarda el contenido en el archivo
        $resultMiddleware = file_put_contents($middlewarePath, $middlewareContent);

        if ($resultMiddleware !== false) {
            $this->showSuccessMessage($output, "Middleware creado con éxito");
            return Command::SUCCESS;  // Devuelve el código de éxito
        } else {
            $errorMessage = error_get_last();
            $output->writeln("<error>Error Error al crear el Middleware: {$errorMessage['message']}</error>");
            return Command::FAILURE;  // Devuelve el código de error
        }
    }
}
