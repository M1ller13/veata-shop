const User = require('../models/User');

module.exports = async (req, res, next) => {
  try {
    // Get user from auth middleware
    const user = await User.findById(req.user.id);

    if (!user) {
      return res.status(404).json({ msg: 'Пользователь не найден' });
    }

    // Check if user is admin
    if (!user.isAdmin) {
      return res.status(403).json({ msg: 'Доступ запрещен. Требуются права администратора' });
    }

    next();
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Ошибка сервера');
  }
}; 