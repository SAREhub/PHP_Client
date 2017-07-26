#################################################
Cookbook
#################################################
Klient używany do tworzenia mikroserwisów implementuje kilka ich rodzajów.
Rozróżniamy aktualnie następujące typy:
- służy do przekazywania informacji w postaci wiadomości, przykładowo
tagów przez brokera RabbitMQ. Obsługuje głównie protokół AMQP. Ponadto ogólnie
wykonuje wszelakie logiczne procesy w tle wiadomości.

* **moduł** - służy do przekazywania informacji w postaci wiadomości, przykładowo tagów przez brokera RabbitMQ. Obsługuje głównie protokół AMQP. Ponadto ogólnie wykonuje wszelakie logiczne procesy w tle wiadomości.

* **API** - wykorzystywane jest do odbierania danych z aplikacji na Frontendzie. Posiada kontrolery oraz obsługuje żądania wysyłając odpowiednie informacje dla modułów.

* **filtr** - filtruje dane.

Tworzenie modułu
====================
Tworzony przez nas moduł obsługuje tzw. Pipeline'y, które zostały opisane w dokumentacji.
Przykładowa ich obsługa jest pokazana w tej fabryce:

.. code-block:: php

   <?php
   /**
    * Class UserTagsPipelineFactory
    * @package SAREhub\Module\UserTags
    */
   class ConfigChangedPipelineFactory {
   	/**
   	 * @var ClientContext
   	 */
   	private $context;

   	/**
   	 * ConfigChangedPipelineFactory constructor.
   	 * @param ClientContext $context
   	 */
   	public function __construct(ClientContext $context) {
   		$this->context = $context;
   	}

   	/**
   	 * @return Pipeline
   	 */
   	public function create() {
   		return Pipeline::start()
   		  ->add($this->getEventUnmarshalingProcessor())
   		  ->add($this->eventTypeRouter());
   	}

   	/**
   	 * @return Router
   	 */
   	public function eventTypeRouter() {
   		$r = Router::newInstance()
   		  ->withRoutingFunction(function (Exchange $exchange) {
   			  $event = EventHelper::extract($exchange->getIn());
   			  return $event->getType();
   		  })
   		  ->addRoute('module_config_changed', $this->reloadRules());
   		$this->context->injectLogger("processPipeline.eventTypeRouter", $r);

   		return $r;
   	}
    ?>

Opisana powyżej fabryka tworzy rurociąg(Pipeline) za pomocą statycznej metody **start()**.
Zwraca ona instancje rurociągu, do którego dodawane są nowe procesory, w których wywołują się
procesy odpowiadające działaniu żądanego kodu. W kodzie powyżej przykładem takiego procesora jest
*Router*, który sprawdza czy wiadomość jest zdeklarowanym typem, jeśli nim jest, wtedy wywołuje metodę
zwracającą inny procesor.
