--
-- Base de données : `sorties`
--

--
-- Déchargement des données de la table `campus`
--


INSERT INTO `campus` (`id`, `nom`) VALUES
(2, 'CHARTRES DE BRETAGNE'),
(3, 'LA ROCHE SUR YON'),
(1, 'SAINT HERBLAIN');

--
-- Déchargement des données de la table `etat`
--

INSERT INTO `etat` (`id`, `libelle`) VALUES
(1, 'Créée'),
(2, 'Ouverte'),
(3, 'Clôturée'),
(4, 'En cours'),
(5, 'Passée'),
(6, 'Annulée');

--
-- Déchargement des données de la table `participant`
--

INSERT INTO `participant` (`id`, `campus_id`, `pseudo`, `roles`, `password`, `nom`, `prenom`, `telephone`, `email`, `champ`, `verif_mdp`) VALUES
(1, 1, 'admin', '[\"ROLE_ADMIN\"]', '$2y$13$KbZxWYQPzfAHl7XIvPOuh.6FEhAKmMF5Jqg9eKkOqZg8kQ8Jl/GCu', 'ADMIN', 'Admin', '0606060606', 'admin@mail.com', '', 0),
(2, 1, 'user', '[\"ROLE_USER\"]', '$2y$13$zYceY7Oy/kykSKNj/Fezc.hdOW4wW7S/2UP.u7oh5yibfLDTYCaWi', 'UTILISATEUR', 'Utilisateur', '0606060606', 'user@mail.com', '', 0);

--
-- Déchargement des données de la table `ville`
--

INSERT INTO `ville` (`id`, `nom`, `code_postal`) VALUES
(1, 'SAINT HERBLAIN', '44800'),
(2, 'HERBLAY', '95220'),
(3, 'CHERBOURG', '50100'),
(4, 'HERBIGNAC', '44410');
