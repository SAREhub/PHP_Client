#################################################
Procesory
#################################################

Czym jest procesor?
===================
Procesor jest klasą, która implementuje metody wywołujące procesy zaimplementowane przez nas.
Przykładowo wysłanie wiadomości będzie rozdzielone na kilka procesów, takich jak: zbudowanie wiadomości,
definiowanie miejsca wysyłki, wysyłka wiadomości. Procesy te wywołane zostaną w metodzie **process()**, która
jest zaimplementowana w interfejsie procesora.
