const jwt = require('jsonwebtoken');
const User = require('../models/User');

module.exports = async (req, res, next) => {
  try {
    let token;

    // Check if token exists in headers
    if (
      req.headers.authorization &&
      req.headers.authorization.startsWith('Bearer')
    ) {
      token = req.headers.authorization.split(' ')[1];
    }

    if (!token) {
      return res.status(401).json({ msg: 'Нет доступа. Требуется авторизация' });
    }

    try {
      // Verify token
      const decoded = jwt.verify(token, process.env.JWT_SECRET);

      // Get user from the token
      req.user = await User.findById(decoded.id).select('-password');

      if (!req.user) {
        return res.status(401).json({ msg: 'Пользователь не найден' });
      }

      next();
    } catch (err) {
      console.error(err.message);
      res.status(401).json({ msg: 'Токен недействителен' });
    }
  } catch (err) {
    console.error(err.message);
    res.status(500).send('Ошибка сервера');
  }
}; 