#################################################
Cookbook
#################################################

Rurociągi
====================

Tworzenie rurociągu
--------------------

Rurociągi w kliencie PHP służą do implementowania procesorów, które wykonują
procesy wymagane do wykonania ustalonych funkcjonalności.

  .. code-block:: PHP

    <?php
      Pipeline::start()
        ->add(new Processor1())
        ->add(new Processor2());
     ?>

W pokazany powyżej sposób tworzymy nowy rurociąg, do którego za pomocą metody **add()**
wrzucamy nowe procesory.

Uruchamianie rurociągu
----------------------

Aby wywołać wykonanie rurociągu należy wywołać metodę **process()** na instancji klasy Pipeline.


.. code-block:: PHP

  <?php>
    Pipeline::start()
    ->add(new Processor1())
    ->process();

W taki sposób akcje, które zostały zdefiniowane w procesorze o nazwie Processor1 zostaną wykonane przez
wywołanie metody process().    
