TESTS AVEC SUBEVENT
Tout est normal → OK
Joueur déjà inscrit → OK
Numéro de licence inconnu → OK (reste à faire le lien d'ajout)
Nom du subevent ne correspond pas → OK
Nom/Prénom ne correspondent pas du tout au numéro de licence → OK  [Texier Clément vs Clement LAMBLAIN]
Nom/Prénom ne correspondent pas trop au numéro de licence → OK avec warning correct [Nicolas LAMBLAIN vs Nicolas-Charles LAMBLAIN]

Attention : nom et prénom correspondent approx et joueur déjà inscrit → demande ignorée.

TESTS SANS SUBEVENT
à faire

j'envoie ça sur le tournoi 64,ça merde : pourquoi ? 

{"data": {"payer": {"email": "benedicte-pierre@sfr.fr", "country": "FRA", "dateOfBirth": "2023-05-18T10:34:00+02:00", "firstName": "Pierre", "lastName": "FRANCOIS"}, "items": [{"payments": [{"id": 28157748, "shareAmount": 1000}], "name": "Jeunes", "user": {"firstName": "Victor", "lastName": "FRAN\u00c7OIS"}, "priceCategory": "Fixed", "customFields": [{"name": "Num\u00e9ro de licence FFE : 1 lettre + 5 chiffres", "type": "TextInput", "answer": "K58066"}, {"name": "Nom du club", "type": "TextInput", "answer": "ECHECS LOISIRS ORANGE"}, {"name": "Code postal du club", "type": "TextInput", "answer": "84100"}], "ticketUrl": "https://www.helloasso.com/assoc iations/valence-echecs/evenements/open-semi-rapide-de-valence/ticket?ticketId=59957273&ag=59957273", "qrCode": "NTk5NTcyNzM6NjM4MjAwMDI4NzU4MTk5NTA3", "tierDescription": "Pensez \u00e0 avoir votre licence FFE \u00e0 jour.\nCat\u00e9gorie : jusqu'\u00e0 20 ans inclus.", "id": 59957273, "amount": 1000, "type": "Registration", "initialAmount": 1000, "state": "Processed"}], "payments": [{"items": [{"id": 59957273, "shareAmount": 1000, "shareItemAmount": 1000}], "cashOutState": "Transfered", "paymentReceiptUrl": "https://www.helloasso.com/associations/valence-echecs/evenements/open-semi-rapide-de-valence/paiement-attestation/59957273", "id": 28157748, "amount": 1000, "date": "2023-05-18T10:34:38.4293708+02:00", "paymentMeans": "Card", "installmentNumber": 1, "state": "Authorized", "meta": {"createdAt": "2023-05-18T10:34:35.8199507+02:00", "updatedAt": "2023-05-18T10:34:38.5333333+02:00"}, "refundOperations": []}], "amount": {"total": 1000, "vat": 0, "discount": 0}, "id": 59957273, "date" : "2023-05-18T10:34:39.5476338+02:00", "formSlug": "open-semi-rapide-de-valence", "formType": "Event", "organizationName": "VALENCE ECHECS", "organizationSlug": "valence echecs", "meta": {"createdAt": "2023-05-18T10:34:35.8199507+02:00", "updatedAt": "2023-05-18T10:34:39.5476338+02:00"}, "isAnonymous": false, "isAmountHidden": false}, "eventType": "Order"}